<template>
        <div class="chat-app">
            <conversation :contact="selectedContact" :messages="messages" @new="saveNewMessage" :user="user"/>
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
                files:[],
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
                    console.log('contactos',response.data);
                    this.contacts = response.data;
                })

            Echo.private(`user.has.loged.In.${this.user.id}`)
                .listen('LogedInUser',(e)=>{
                    console.log('un usuario inicio sesion',e);
                })
            
            Echo.private(`user.notifications.${this.user.id}`)
                .notification((notification) => {
                    console.log('notificacion tipo',notification);
                    if(notification.notification == "user_conected"){

                        let contact = this.contacts.filter(function(c){
                            return c.id === notification.user_id;
                        });

                        contact[0].online = 1;

                        this.$toast.success({
                            title:`${notification.user_name}`,
                            color:'rgb(70, 171, 27,1)',
                            message:`Ha Iniciado Sesión`,
                            position:'top right',
                            showDuration: 1500,
                            hideDuration: 1500,
                            timeOut: 6000,
                            closeButton: true,
                            icon: `${contact[0].profile_img}`
                        });
                    }
                    else if(notification.notification == "user_disconnected"){
                        let contact = this.contacts.filter(function(c){
                            return c.id === notification.user_id;
                        });

                        contact[0].online = 0;

                        this.$toast.success({
                            title:`${notification.user_name}`,
                            color:'rgba(227, 93, 16,1)',
                            message:`se ha Desconectado`,
                            position:'top right',
                            showDuration: 1500,
                            hideDuration: 1500,
                            timeOut: 7000,
                            closeButton: true,
                            icon: `${contact[0].profile_img}`
                        });
                    }
                    else if(notification.notification == "message_watched"){
                        if(this.selectedContact && this.selectedContact.id == notification.to){
                            axios.get(`/conversation/${this.selectedContact.id}`)
                            .then((response)=>{
                                this.messages = response.data;
                                console.log('mensajes',this.messages);
                            })
                        }
                    }
                    
                });
        },
        methods:{
            startConversationWith(contact){
                //cada vez que se comienza una conversacion con alguien reseteamos los mensajes no leidos
                this.updateUnreadMessages(contact,true);
                axios.get(`/conversation/${contact.id}`)
                    .then((response)=>{
                        this.messages = response.data.messages;
                        this.files = response.data.files;
                        console.log('mensajes',this.messages);
                        console.log('archivos',this.files);

                        this.selectedContact = contact;
                    })
            },

            saveNewMessage(message){
                console.log('guardando el nuevo mensaje',message);
                this.messages.push(message);
            },

            handleIncoming(message){
                if(this.selectedContact && message.from == this.selectedContact.id){
                    // si el mensaje que se recibe es del contacto que estoy hablando se agrega
                    //message.read = 1;
                    this.saveNewMessage(message);
                    //marcar el mensaje como leído para avisarle al otro contacto
                    axios.post(`/message_read/${message.id}`)
                            .then((response)=>{
                                console.log('mensaje actualizado como leído',response.data);
                            });
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

