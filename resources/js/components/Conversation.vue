<template>
    <div class="conversation">
        <h1>{{contact?contact.name:'Seleccione un Contacto'}}</h1>
        <messages-feed :contact="contact" :messages="messages"></messages-feed>
        <message-box @send="sendMessage"></message-box>
    </div>
</template>
<script>
import MessagesFeed from './MessagesFeed';
import MessageBox from './MessageBox';
export default {
    props:{
        contact:{
            type:Object,
            default:null
        },
        messages:{
            type:Array,
            default:null
        }
    },
    methods:{
        sendMessage(text){
            if(!this.contact){
                return;
            }

            axios.post(`/conversation/send`,{
                contact_id: this.contact.id,
                text:text
            }).then((response)=>{
                this.$emit('new',response.data);
            })
            console.log(text);
        }
    },
    components:{
        'messages-feed':MessagesFeed,
        'message-box':MessageBox
    }
}
</script>
<style lang="scss" scoped>
.conversation{
    flex:5;
    display:flex;
    flex-direction:column;
    justify-content: space-between;

    h1{
        font-size: 20px;
        padding:10px;
        margin:0;
        border-bottom:1px solid lightgray;
    }
}
</style>


