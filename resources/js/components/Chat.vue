<template>
        <div class="chat-app">
            <conversation :contact="selectedContact" :messages="messages" @new="saveNewMessage"/>
            <contactsList :contacts="contacts" @selected="startConversationWith"/>
        </div>
</template>

<script>
import Conversation from './Conversation';
import ContactsList from './ContactsList';
    export default {
        props:{
            user:{
                type:Object,
                required:true
            }
        },
        data(){
            return{
                selectedContact:null,
                messages:[],
                contacts:[]
            }
        },
        mounted() {
            console.log('Component mounted.');
            console.log(this.user);

            Echo.private(`messages.${this.user.id}`)
                .listen('NewMessage',(e)=>{
                    console.log('evento recepcionado',e);
                    this.handleIncoming(e.message);
                })
            axios.get('/contacts')
                .then((response)=>{
                    console.log(response.data);
                    this.contacts = response.data;
                })
        },
        methods:{
            startConversationWith(contact){
                //cada vez que se comienza una conversacion con alguien reseteamos los mensajes no leidos
                this.updateUnreadMessages(contact,true);
                axios.get(`/conversation/${contact.id}`)
                    .then((response)=>{
                        this.messages = response.data;
                        this.selectedContact = contact;
                    })
            },

            saveNewMessage(message){
                this.messages.push(message);
            },

            handleIncoming(message){
                if(this.selectedContact && message.from == this.selectedContact.id){
                    // si el mensaje que se recibe es del contacto que estoy hablando se agrega
                    this.saveNewMessage(message);
                    return;
                }

                //si el mensaje que se recibe no es del contacto con el que estoy hablando actualizo 
                //los mensajes no leídos del contacto y le sumo 1
                console.log(message);
                this.updateUnreadMessages(message.from_contact,false);
                //alert(message.text);
            },

            updateUnreadMessages(contact,reset){
                //actualiza el valor de los mensajes no leidos, recibe el contacto y true o false
                this.contacts = this.contacts.map((single)=>{
                    if(single.id != contact.id){
                        return single;
                    }

                    if(reset){
                        single.unread = 0;
                    }
                    else{
                        single.unread += 1;
                    }
                    return single;
                });
            }
        },
        components:{
            'conversation':Conversation,
            'contactsList':ContactsList
        }
    }
</script>
<style lang="scss" scoped>
.chat-app{
    display:flex;
}
</style>

