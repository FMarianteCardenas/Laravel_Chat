<template>
    <div class="composer">
        <div class="tipying">
            <span v-if="tipying != ''">{{tipying}}</span>
        </div>
        <textarea v-model="message" @keydown.enter="send" placeholder="Escriba un mensaje..."></textarea>
    </div>
</template>
<script>
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
    },
    data(){
        return{
            message:'',
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
        send(e){
            e.preventDefault();
            if(this.message === ''){
                return;
            }
            this.$emit('send',this.message);
            this.message = '';
        }
    },
    watch:{
        message(){
            Echo.private(`chat.${this.contact.id}`)
                .whisper('typing', {
                    name: this.user.name
            });
        },
        tipying(){
            setTimeout(()=>{
                this.tipying = '';
            },4000);
        }
    }
}
</script>
<style lang="scss" scoped>
.composer textarea{
    width:96%;
    margin:10px;
    resize:none;
    border-radius: 4px;
    border: 1px solid lightgray;
    padding:6px;
}

.tipying{
        max-height: 10%;
        text-align: right;
        margin-right: 10px;
        margin-top: 5px;
}


.tiblock {
    align-items: center;
    display: flex;
    height: auto;
}

.ticontainer .tidot {
    background-color: #90949c;
}

.tidot {
    -webkit-animation: mercuryTypingAnimation 1.5s infinite ease-in-out;
    border-radius: 2px;
    display: inline-block;
    height: 4px;
    margin-right: 2px;
    width: 4px;
}

@-webkit-keyframes mercuryTypingAnimation{
0%{
  -webkit-transform:translateY(0px)
}
28%{
  -webkit-transform:translateY(-5px)
}
44%{
  -webkit-transform:translateY(0px)
}
}

.tidot:nth-child(1){
-webkit-animation-delay:200ms;
}
.tidot:nth-child(2){
-webkit-animation-delay:300ms;
}
.tidot:nth-child(3){
-webkit-animation-delay:400ms;
}
</style>
