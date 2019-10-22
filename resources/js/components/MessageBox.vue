<template>
    <div class="composer">

        <label class="dropdown">
            <div class="dd-button" @click="showHidePicker">😃</div>
            <!-- <input type="checkbox" class="dd-input" id="test"> -->
            <div :class="`dd-menu ${showpicker}`" id="dd-menu">
                <v-emoji-picker :pack="pack" @select="selectEmoji" labelSearch="buscar..." />
            </div>
        </label>
        <button class="dd-button" @click="showHideFileUpload">📎</button>
        <div class="container" v-if="showFileUpload">
                    <div class="large-12 medium-12 small-12 cell">
                    <label>
                        <input type="file" id="file" ref="file" v-on:change="handleFileUpload()"/>
                    </label>
                        <button v-on:click="submitFile()">Enviar</button>
                    </div>
        </div>
        
        <div class="tipying">
            <span v-if="tipying != ''">{{tipying}}</span>
        </div>
        <textarea v-model="message" @keydown.enter="send" placeholder="Escriba un mensaje..."></textarea>
    </div>
</template>
<script>
import VEmojiPicker from 'v-emoji-picker';
import packData from 'v-emoji-picker/data/emojis.json';
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
            file:'',
            tipying:'',
            pack:packData,
            showpicker:'hide',
            showFileUpload:false,
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
        },
        showHidePicker(){
            if(this.showpicker == 'hide'){
                this.showpicker = 'show';
            }
            else{
                this.showpicker = 'hide';
            }
        },
        showHideFileUpload(){
            this.showFileUpload = !this.showFileUpload;
        },
        selectEmoji(emoji) {
            console.log(emoji);
            this.message += emoji.emoji;
        },
        handleFileUpload(){
            this.file = this.$refs.file.files[0];
        },
         /*
        Submits the file to the server
      */
      submitFile(){
          window.location.href = '/download/1';
          return;
        /*
                Initialize the form data
            */
            let formData = new FormData();

            /*
                Add the form data we need to submit
            */
            formData.append('file', this.file);
            formData.append('to',this.contact.id);

        /*
          Make the request to the POST /single-file URL
        */
        axios.post( '/send-file',formData,{headers: {'Content-Type': 'multipart/form-data'}})
            .then((response)=>{
                console.log(response);
            }).catch((response)=>{
            console.log(response);
        });
      },
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
    },
    components:{
        'v-emoji-picker':VEmojiPicker
    }
}
</script>
<style lang="scss" scoped>
.composer textarea{
    width:96%;
    margin-top:5px;
    margin:10px;
    resize:none;
    border-radius: 4px;
    border: 1px solid lightgray;
    padding:6px;
}

.composer button{
    border-radius: 5px;
    margin-left: 10px;
    margin-top: 10px;
}
.tipying{
        max-height: 10%;
        text-align: right;
        margin-right: 10px;
        margin-top: 5px;
}





.dropdown {
  display: inline-block;
  position: relative;
}

.dd-button {
  margin-left: 10px;
  margin-top: 5px;
  display: inline-block;
  border: 1px solid gray;
  border-radius: 4px;
  padding: 10px 30px 10px 10px;
  background-color: #ffffff;
  cursor: pointer;
  white-space: nowrap;
}

.dd-button:after {
  content: '';
  position: absolute;
  top: 50%;
  right: 15px;
  transform: translateY(-50%);
  width: 0; 
  height: 0; 
  border-left: 5px solid transparent;
  border-right: 5px solid transparent;
  border-top: 5px solid black;
}

.dd-button:hover {
  background-color: #eeeeee;
}


.dd-input {
  display: none;
}

.dd-menu {
  position: absolute;
  border: 1px solid #ccc;
  border-radius: 4px;
  padding: 0;
  margin: 2px 0 0 0;
  box-shadow: 0 0 6px 0 rgba(0,0,0,0.1);
  background-color: #ffffff;
  list-style-type: none;
  bottom: 100%;
  left: 12%;
}
.dd-input + .dd-menu {
  display: none;
} 

.dd-input:checked + .dd-menu {
  display: block;
} 

.hide{
    display:none;
}
.show{
    display:block;
}

.dd-menu li {
  padding: 10px 20px;
  cursor: pointer;
  white-space: nowrap;
}

.dd-menu li:hover {
  background-color: #f6f6f6;
}

.dd-menu li a {
  display: block;
  margin: -10px -20px;
  padding: 10px 20px;
}

.dd-menu li.divider{
  padding: 0;
  border-bottom: 1px solid #cccccc;
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