<template>
    <div class="contact-list">
        <ul>
            <li v-for="contact in sortContactByUnread" :key="contact.id" @click="selectContact(contact)" :class="{'selected':contact===selected}">
                <span class="connected neon" v-if="contact.online == 1">c</span>
                <div class="avatar">
                    <img :src="contact.profile_img" :alt="contact.name">
                </div>
                <div class="contact">
                    <p class="name">{{contact.name}}</p>
                    <p>{{contact.email}}</p>
                    <p class="neon" v-if="contact.online == 1">Conectado(a)</p>
                </div>
                <span class="unread" v-if="contact.unread > 0">{{contact.unread}}</span>
            </li>
        </ul>
    </div>
</template>
<script>
export default {
    props:{
        contacts:{
            type:Array,
            default:[]
        }
    },
    data(){
        return{
            selected:this.contacts.length?this.contacts[0]:null
        }
    },
    methods:{
        selectContact(contact){
            this.selected = contact;
            this.$emit('selected',contact);
        }
    },
    computed:{
        //propiedad computada que ordena el array de contactos por numero de mensajes no leídos primero
        sortContactByUnread(){
            return _.sortBy(this.contacts,[(contact)=>{
                //si el contacto actual es con el que estoy hablando retorna la propiedad infinity en vez
                //de el valor de unread del contacto
                if(contact == this.selected){
                    return Infinity;
                }
                return contact.unread;
            }]).reverse();
        }
    }
}
</script>
<style lang="scss" scoped>
.contact-list{
    flex:2;
    max-height: 600px;
    overflow: scroll;
    border-left: 1px solid #2aba25;

    ul{
        list-style-type: none;
        padding-left: 0;

        li{
            display:flex;
            padding:2px;
            border-bottom: 1px solid #aaaaaa;
            height: 80px;
            position: relative;
            cursor:pointer;

            &.selected{
                background:rgba(255,221,3,0.7);
            }

            span.unread{
                background:#82e0a8;
                color:#fff;
                position:absolute;
                right:11px;
                top:20px;
                display:flex;
                font-weight:700;
                min-width: 20px;
                justify-content: center;
                align-items: center;
                line-height: 20px;
                padding: 0 4px;
                border-radius:3px;
            }

            span.connected{
                background:#35e653;
                color:#35e653;
                position:absolute;
                right:11px;
                top:10px;
                display:flex;
                font-weight:500;
                max-width: 7px;
                justify-content: center;
                align-items: center;
                line-height: 9px;
                padding: 0 4px;
                border-radius:50%;
            }

            .neon {
                color: #35e653;
                text-shadow:
                    0 0 5px rgba(70, 171, 27,1),
                    0 0 10px rgba(70, 171, 27,1),
                    0 0 20px rgba(70, 171, 27,1),
                    0 0 40px rgba(55, 122, 26,1),
                    0 0 80px rgba(55, 122, 26,1),
                    0 0 90px rgba(55, 122, 26,1),
                    0 0 100px rgba(55, 122, 26,1),
                    0 0 140px rgba(55, 122, 26,1),
                    0 0 180px rgba(55, 122, 26,1);
            }

            .avatar{
                flex:1;
                display:flex;
                align-items:center;

                img{
                    width:35px;
                    border-radius:50%;
                    margin:0 auto;
                }
            }

            .contact{
                flex:3;
                font-size: 10px;
                overflow: hidden;
                display: flex;
                flex-direction:column;
                justify-content:center;

                p{
                    margin:0;
                    &.name{
                        font-weight:bold;
                    }

                    &.neon {
                            color: #2bbd30;
                            text-shadow:
                    0 0 5px rgba(70, 171, 27,1);
                            }
                }
                
            }
        }
    }
}
</style>