<template>
    <div class="conversation">
        <h1 class="head-app">{{contact?contact.name:'Seleccione un Contacto'}}</h1>
        <messages-feed :contact="contact" :messages="messages" :user="user"></messages-feed>
        <message-box @send="sendMessage" :user="user" :contact="contact"></message-box>
        <img width="35" height="35" :src="`/storage/uploads/B1RqrrkVLPJLIaDRBzRM6EjLLqBSVYjcKcF6fDDo.jpeg`" alt="">
    </div>
</template>
<script>
import MessagesFeed from './MessagesFeed';
import MessageBox from './MessageBox';
import { setTimeout } from 'timers';
export default {
    props:{
        user:{
            type:Object,
            required:true
        },
        contact:{
            type:Object,
            default:null
        },
        messages:{
            type:Array,
            default:null
        },
    },
    data(){
        return{
            tipying:''
        }
    },
    mounted(){
        Echo.private(`chat.${this.user.id}`)
        .listenForWhisper('typing', (e) => {
            this.tipying = `${e.name} esta escribiendo...`;
            console.log(`${e.name} esta escribiendo...`);
        });
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
    watch:{
        tipying(){
            setTimeout(()=>{
                this.tipying = '';
            },1500);
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
        border: 1px solid rgba(255, 231, 78,1);
        border-bottom:1px solid lightgray;
        color: #000000;
    }

    .head-app{
        background-color: rgba(255, 231, 78,1);
    }
}
</style>


