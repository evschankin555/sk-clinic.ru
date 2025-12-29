<?php
function getStyle(){
    ?>
<style type="text/css">
@font-face{font-family:Gerbera-Light;font-style:normal;font-weight:400;font-display:swap;src:url(/fonts/Gerbera-Light.woff) format("woff"),url(/fonts/Gerbera-Light.woff2) format("woff2")}@font-face{font-family:Gilroy-Light;font-style:normal;font-weight:400;font-display:swap;src:url(/fonts/Gilroy-Light.woff) format("woff"),url(/fonts/Gilroy-Light.woff2) format("woff2")}@font-face{font-family:Gerbera;font-style:normal;font-weight:400;font-display:swap;src:url(/fonts/Gerbera.woff) format("woff"),url(/fonts/Gerbera.woff2) format("woff2")}@font-face{font-family:Gilroy-RegularItalic;font-style:normal;font-weight:400;font-display:swap;src:url(/fonts/Gilroy-RegularItalic.woff) format("woff"),url(/fonts/Gilroy-RegularItalic.woff2) format("woff2")}@font-face{font-family:Gilroy;font-style:normal;font-weight:400;font-display:swap;src:url(/fonts/Gilroy.woff) format("woff"),url(/fonts/Gilroy.woff2) format("woff2")}@font-face{font-family:Gerbera-Medium;font-style:normal;font-weight:400;font-display:swap;src:url(/fonts/Gerbera-Medium.woff) format("woff"),url(/fonts/Gerbera-Medium.woff2) format("woff2")}@font-face{font-family:Gilroy-Medium;font-style:normal;font-weight:400;font-display:swap;src:url(/fonts/Gilroy-Medium.woff) format("woff"),url(/fonts/Gilroy-Medium.woff2) format("woff2")}@font-face{font-family:Gilroy-SemiBold;font-style:normal;font-weight:400;font-display:swap;src:url(/fonts/Gilroy-SemiBold.woff) format("woff"),url(/fonts/Gilroy-SemiBold.woff2) format("woff2")}@font-face{font-family:Gerbera-Bold;font-style:normal;font-weight:400;font-display:swap;src:url(/fonts/Gerbera-Bold.woff) format("woff"),url(/fonts/Gerbera-Bold.woff2) format("woff2")}@font-face{font-family:Gilroy-Bold;font-style:normal;font-weight:400;font-display:swap;src:url(/fonts/Gilroy-Bold.woff) format("woff"),url(/fonts/Gilroy-Bold.woff2) format("woff2")}
.connection-page {
  width: 360px;
  padding: 8% 0 0;
  margin: auto;
}
.form {
  position: relative;
  z-index: 1;
  background: #ffffff;
  max-width: 360px;
  margin: 0 auto 100px;
  padding: 45px;
  text-align: center;
  box-shadow: 0 0 20px 0 rgba(0, 0, 0, 0.2), 0 5px 5px 0 rgba(0, 0, 0, 0.24);
}
.form input {
  font-family: Gilroy,Arial,sans-serif;
  outline: 0;
  background: #f2f2f2;
  width: 100%;
  border: 0;
  margin: 0 0 15px;
  padding: 15px;
  box-sizing: border-box;
  font-size: 14px;
}
.form button {
  font-family: Gilroy,Arial,sans-serif;
  text-transform: uppercase;
  outline: 0;
  background: #1d2327;
  width: 100%;
  border: 0;
  padding: 15px;
  color: #ffffff;
  font-size: 14px;
  -webkit-transition: all 0.3 ease;
  transition: all 0.3 ease;
  cursor: pointer;
}
.form button:hover,
.form button:active,
.form button:focus {
  background: #1d2327;
}
.form .message {
  margin: 15px 0 0;
  color: #b3b3b3;
  font-size: 12px;
}
.form .message a {
  color: #1d2327;
  text-decoration: none;
}
.form .register-form {
  display: none;
}
.container {
  position: relative;
  z-index: 1;
  max-width: 300px;
  margin: 0 auto;
}
.container:before,
.container:after {
  content: "";
  display: block;
  clear: both;
}
.container .info {
  margin: 50px auto;
  text-align: center;
}
.container .info h1 {
  margin: 0 0 15px;
  padding: 0;
  font-size: 36px;
  font-weight: 300;
  color: #1a1a1a;
}
.container .info span {
  color: #4d4d4d;
  font-size: 12px;
}
.container .info span a {
  color: #000000;
  text-decoration: none;
}
.container .info span .fa {
  color: #ef3b3a;
}
body {
  background: #E9E4E1 ;
    background: linear-gradient( 90deg, #E9E4E1 0%, #E9E4E1 50% );
  font-family: Gilroy,Arial,sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}
.php-error #adminmenuback, .php-error #adminmenuwrap {
    margin-top: 0;
}
#wpbody-content {
  text-align: left;
    color: #ffffff;
    font-size: 44px;
  font-family: Gilroy,Arial,sans-serif;
  }
h1 {
    color: #646566;
    font-size: 44px;
    margin: 0.67em 0;
    padding: 20px 0 20px 0;
    font-weight: bold;
}
h1 span {
  font-size: 13px;
  display: block;
  padding-left: 4px;
}
.tabs {
  width: calc(100% - 90px);
  float: none;
  list-style: none;
  position: relative;
  margin: 40px 0 0 10px;
  text-align: left;
}
.tabs li {
  float: left;
  display: block;
}
.tabs input[type="radio"] {
  position: absolute;
  top: 0;
  left: -9999px;
}
.tabs label {
    display: block;
    padding: 17px 21px;
    border-radius: 10px 10px 0 0;
    font-size: 20px;
    font-weight: normal;
    text-transform: uppercase;
    background: #1d232799;
    cursor: pointer;
    position: relative;
    top: 4px;
    -moz-transition: all 0.2s ease-in-out;
    -o-transition: all 0.2s ease-in-out;
    -webkit-transition: all 0.2s ease-in-out;
    transition: all 0.2s ease-in-out;
    font-weight: bold;
}
.tabs label:hover {
  background: #363633;
}
.tabs .tab-content {
  z-index: 2;
  display: none;
  overflow: inherit;
  width: 100%;
  font-size: 17px;
  line-height: 25px;
  padding: 25px;
  position: absolute;
  top: 53px;
  left: 0;
  background: #363633;
}
.tabs [id^="tab"]:checked + label {
  padding-top: 17px;
  background: #363633;
}
.tabs [id^="tab"]:checked ~ [id^="tab-content"] {
  display: block;
    border-radius: 0 10px 10px 10px;
}

p.link {
  clear: both;
  margin: 380px 0 0 15px;
}
p.link a {
  text-transform: uppercase;
  text-decoration: none;
  display: inline-block;
  color: #fff;
  padding: 5px 10px;
  margin: 0 5px;
  background-color: #363633;
  -moz-transition: all 0.2s ease-in;
  -o-transition: all 0.2s ease-in;
  -webkit-transition: all 0.2s ease-in;
  transition: all 0.2s ease-in;
}
p.link a:hover {
  background-color: #4f7a38;
}
input {
    color: #1d2327!important;
}

p {
    color: #fff!important;
    padding: 0;
    margin: 2px 0;
}
code {
    padding: 3px 5px 2px;
    margin: 0 1px;
    background: #f0f0f1;
    background: #363633;
    font-size: 13px;
    color: #e8b76b;
    border: 1px solid #33333396;
    padding: 0px 5px;
    border-radius: 5px;
    width: calc(100% - 40px);
    min-height: 20px;
    height: 100%;
}
.stolb h2{
    min-width: 100%;
    text-align: center;
    color: #2c3338;
    font-size: 24px;
}
.fields-data{
    width: 100%;
    height: 100%;
    background-color: #F9F5EC;
    border-radius: 10px;
    color: #3333339e;
    font-size: 14px;
    display: flex;
    justify-content: center;
    align-content: start;
    flex-direction: column;
}
.all-content{
    width: 100%;
    height: 100%;
    border-radius: 10px;
    margin-top: 10px;
    color: #3333339e;
    font-size: 14px;
    display: flex;
    justify-content: end;
    align-content: start;
    flex-direction: row;
    justify-content: space-between;
}
.stolb{
    width: calc(20% - 30px);
    height: 100%;
    padding: 10px;
    background-color: #F9F5EC;
    border-radius: 10px;
    margin-top: 10px;
    color: #3333339e;
    font-size: 14px;
    display: flex;
    align-content: start;
    flex-direction: column;
}
.heavyTable input[type="file"]::-webkit-file-upload-button {
  background-color: #363633;
  color: white;
  border: none;
  padding: 8px 20px;
  border-radius: 5px;
  cursor: pointer;
}
.heavyTable input[type="file"]::-moz-file-upload-button {
  background-color: #363633;
  color: white;
  border: none;
  padding: 8px 20px;
  border-radius: 5px;
  cursor: pointer;
}
.heavyTable input[type="file"]::-ms-upload-button {
  background-color: #363633;
  color: white;
  border: none;
  padding: 8px 20px;
  border-radius: 5px;
  cursor: pointer;
}
.heavyTable input[type="file"]::-webkit-file-upload-button:hover{
  background-color: #76b851!important;
}
.heavyTable input[type="file"]::-moz-file-upload-button:hover{
  background-color: #76b851!important;
}
.heavyTable input[type="file"]::-ms-upload-button:hover {
  background-color: #76b851!important;
}
.row_buttons{
    display: flex;
}
.add-row-button {
    background-color: #C29A5C;
    color: #fff;
    font-weight: 700;
    font-synthesis: none;
    line-height: 1.375rem;
    letter-spacing: .05em;
    text-transform: uppercase;
    display: block;
    -webkit-transition: background-color .3s,opacity .3s;
    -o-transition: background-color .3s,opacity .3s;
    transition: background-color .3s,opacity .3s;
    border: none;
    padding: 8px 20px;
    border-radius: 5px;
    cursor: pointer;
    margin-bottom: 10px;
    font-weight: bold;
    box-shadow: 0 0 20px 0 rgb(0 0 0 / 20%), 0 5px 5px 0 rgb(0 0 0 / 24%);
}

.add-row-button{
    font-family: Gilroy,Arial,sans-serif;
    font-weight: 400;
    font-synthesis: none;
    margin-right: 10px;
}

.add-row-button:hover {
    background-color: #E8B76B
}

.add-row-button:active {
    transform: translateY(2px) translateX(0px);
    background-color: #AB8140
}

.popup-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.6);
  z-index: 999;
}
.popup-content {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(calc(-50% + 100px), -50%);
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    background-color: #1d2327;
    border-radius: 15px;
    padding: 20px;
    z-index: 1000;
    backdrop-filter: blur(15px);
    border: 1px solid #16181a;
    box-shadow: 0 0 10px 0 rgb(0 0 0 / 33%), 0 5px 12px 0 rgb(0 0 0 / 42%);
}
.popup-content button {
  background-color: #76b851;
  color: white;
  border: none;
  padding: 8px 20px;
  border-radius: 5px;
  cursor: pointer;
  margin-bottom: 10px;
  font-weight: bold;
  box-shadow: 0 0 20px 0 rgb(0 0 0 / 20%), 0 5px 5px 0 rgb(0 0 0 / 24%);
}

.popup-content button:hover {
  background-color: #79b251;
}

.popup-content button:active {
  background-color: #79b251;
  box-shadow: 0px 0px 0px #5b8d38;
  transform: translateY(2px) translateX(0px);
}

.popup-content .cancel {
  background-color: #e74c3c;
}

.popup-content .cancel:hover {
  background-color: #e6675a;
}

.popup-content .cancel:active {
  background-color: #e6675a;
  box-shadow: 0px 0px 0px #e74c3c;
  transform: translateY(2px) translateX(0px);
}

.popup-content .save {
  background-color: #2ecc71;
}

.popup-content .save:hover {
  background-color: #36d884;
}

.popup-content .save:active {
  background-color: #36d884;
  box-shadow: 0px 0px 0px #2ecc71;
  transform: translateY(2px) translateX(0px);
}
.delete {
  background-color: #e74c3c;
  color: white;
  border: none;
  padding: 6px;
  border-radius: 5px;
  cursor: pointer;
  font-weight: bold;
  box-shadow: 0 0 2px 0 rgb(0 0 0 / 33%), 0 3px 3px 0 rgb(0 0 0 / 37%);
}

.delete i {
  font-size: 18px;
}

.delete:hover {
  background-color: #ff5b4f;
}

.delete:active {
  background-color: #ff5b4f;
  box-shadow: 0px 0px 0px #c9302c;
  transform: translateY(2px) translateX(0px);
}

.popup-buttons{
                        display: flex;
                        flex-direction: row;
                        justify-content: space-evenly;
                        width: 100%;
                        margin-top: 20px;
}
.heavyTable td {
    position: relative;
    text-align: center;
    padding: 3px !important;
}

.heavyTable td img {
    display: inline-block;
    vertical-align: middle;
    margin-right: 10px;
}

.heavyTable td input[type="file"] {
    display: inline-block;
    vertical-align: middle;
    position: absolute;
    top: 0;
    right: 0;
    opacity: 0;
    filter: alpha(opacity=0);
    font-size: 100px;
    height: 100%;
    width: 100%;
    cursor: pointer;
    z-index: 1;
}
.open {
  background-color: #3498db;
  color: white;
  border: none;
  padding: 6px;
  border-radius: 5px;
  cursor: pointer;
  font-weight: bold;
  box-shadow: 0 0 2px 0 rgb(0 0 0 / 33%), 0 3px 3px 0 rgb(0 0 0 / 37%);
}

.open i {
  font-size: 18px;
}

.open:hover {
  background-color: #2980b9;
}

.open:active {
  background-color: #2980b9;
  box-shadow: 0px 0px 0px #c9302c;
  transform: translateY(2px) translateX(0px);
}
.open .dashicons, .dashicons-before:before{
    vertical-align: middle;
}
.add-button {
  background-color: #76b851;
  color: white;
  border: none;
  padding: 4px 8px 2px 8px;
  border-radius: 5px;
  cursor: pointer;
  font-weight: bold;
  box-shadow: 0 0 2px 0 rgb(0 0 0 / 33%), 0 3px 3px 0 rgb(0 0 0 / 37%);
}

.add-button i {
  font-size: 18px;
}

.add-button:hover {
  background-color: #87cc61;
}

.add-button:active {
  background-color: #76b851;
  box-shadow: 0px 0px 0px #c9302c;
  transform: translateY(2px) translateX(0px);
}
.add-button .dashicons, .dashicons-before:before{
    vertical-align: middle;
}
.remove-button {
  background-color: #e74c3c;
  color: white;
  border: none;
  padding: 4px 8px 2px 8px;
  border-radius: 5px;
  cursor: pointer;
  font-weight: bold;
  box-shadow: 0 0 2px 0 rgb(0 0 0 / 33%), 0 3px 3px 0 rgb(0 0 0 / 37%);
}

.remove-button i {
  font-size: 18px;
}

.remove-button:hover {
  background-color: #d7403c;
}

.remove-button:active {
  background-color: #c9302c;
  box-shadow: 0px 0px 0px #c9302c;
  transform: translateY(2px) translateX(0px);
}
.remove-button .dashicons, .dashicons-before:before{
    vertical-align: middle;
}
#close_chestThings{
cursor: pointer;
}
#my td:nth-child(3) {
    text-align: center;
    background-color: #d3ffa7;
}
.group_val_text_row img{
    width: 16px;
    height: 11px;
    padding: 3px!important;
    background-color: #e8b76b;
    border-radius: 2px;
    margin: 4px
}
.section{
    max-height: 250px !important;
}
.delete-inventory {
    background-color: #e74c3c;
    color: white;
    border: none;
    padding: 6px;
    border-radius: 5px;
    cursor: pointer;
    font-weight: bold;
    box-shadow: 0 0 2px 0 rgb(0 0 0 / 33%), 0 3px 3px 0 rgb(0 0 0 / 37%);
}
 .val_text {
     width: calc(100% - 20px);
     height: 100%;
     padding: 10px;
     background-color: #F9F5EC;
     border-radius: 10px;
     margin-top: 10px;
     color: #3333339e;
     font-size: 14px;
     display: flex;
     justify-content: flex-start;
     align-content: start;
     flex-direction: column;
 }
.val_text img{
    padding: 6px 6px 6px 0;
}
.group_val_text{
    display: flex;
    width: calc(100% - 10px);
    margin-right: 10px;
    height: 100%;
    margin-top: 10px;
}
.group_val_text_row{
    display: flex;
    flex-direction: column;
}
.val_text_ru, .val_text_en{
    color: #33342E;
    border: 1px solid #33333396;
    padding: 0px 5px;
    border-radius: 5px;
    width: 100%;
    min-height: 20px;
    height: 100%;
}

</style><?php
}