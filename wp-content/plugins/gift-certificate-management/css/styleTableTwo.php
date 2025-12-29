<?php
function getStyleTableTwo() {
    ?>
    <style type="text/css">
        td {
            background-color: #f5f5f5;
        }
        .editable{
            background-color: #ffffff;
        }
        td.selected {
            background: #d7e4ef;
            z-index: ;
        }
        td input {
            font-size: 14px;
            background: none;
            outline: none;
            border: 0;
            display: table-cell;
            height: 100%;
            width: 100%;
        }
        td input:focus {
            box-shadow: 0 1px 0 steelblue;
            color: steelblue;
        }
        ::-moz-selection {
            background: steelblue;
            color: white;
        }
        ::selection {
            background: steelblue;
            color: white;
        }
        .content {
            color: white;
            text-align: center;
        }
        .content p,
        .content pre,
        .content h2 {
            text-align: left;
        }
        .content pre {
            padding: 1.2em 0 0.5em;
            background: white;
            background: rgba(255, 255, 255, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.9);
            color: #38678f;
        }
        .content .download {
            margin: auto;
            background: rgba(255, 255, 255, 0.1);
            display: inline-block;
            padding: 1em 1em;
            border-radius: 12em;
            margin-bottom: 2em;
        }
        .content .button {
            display: inline-block;
            text-decoration: none;
            color: white;
            height: 48px;
            line-height: 48px;
            padding: 0 20px;
            border-radius: 24px;
            border: 1px solid #38678f;
            background: steelblue;
            box-shadow: 0 1px 0 rgba(255, 255, 255, 0.1), inset 0 1px 3px rgba(255, 255, 255, 0.2);
            transition: all 0.1s;
        }
        .content .button:hover {
            background: #4f8aba;
            box-shadow: 0 1px 0 rgba(255, 255, 255, 0.1), inset 0 0 10px rgba(255, 255, 255, 0.1);
        }
        .content .button:active {
            color: #294d6b;
            background: #427aa9;
            box-shadow: 0 1px 0 rgba(255, 255, 255, 0.1), inset 0 0 5px rgba(0, 0, 0, 0.2);
        }
        .content .button:focus {
            outline: none;
        }


        input[type="submit"], input[type="file"]::file-selector-button
        {
            background-color: steelblue;
            padding: 10px;
            border: 1px solid #38678f;
            border-radius: 5px;
            cursor:pointer;
            color: #ffffff;
        }
        input[type="submit"]:hover, input[type="file"]::file-selector-button:hover
        {
            background-color: #4689c1;
        }
        .searchdata{
            z-index: 1000;
        }
        .section{

            height: 350px;padding-bottom: 10px;display: block;overflow: hidden;overflow-y: scroll;
        }
        .section::-webkit-scrollbar {
            width: 10px;
        }

        .section::-webkit-scrollbar-track {
            -webkit-box-shadow: 5px 5px 5px -5px rgba(34, 60, 80, 0.2) inset;
            background-color: #3c434a;
            border-radius: 6px;
        }

        .section::-webkit-scrollbar-thumb {
            border-radius: 6px;
            background: linear-gradient(180deg, #4c545c, #545c64);
        }

        #modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        #modal-status {
            width: 300px;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        #modal-status h2 {
            margin-bottom: 20px;
        }

        #status-select {
            margin-bottom: 20px;
        }

        #modal-buttons {
            display: flex;
            justify-content: center;
            width: 100%;
        }

        #save-status,
        #cancel-status {
            margin-top: 10px;
            padding: 5px 10px;
        }

        #save-status, #create-certificate {
            background: #4CAF50; /* Green */
            border: none;
            color: white;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            transition-duration: 0.4s;
            cursor: pointer;
            border-radius: 5px;
        }

        #save-status:hover, #create-certificate:hover {
            background-color: #45a049;
            color: white;
        }
         #create-certificate {
             padding: 5px 10px;
             margin-bottom: 10px;
        }

        #cancel-status {
            background-color: #f44336; /* Red */
            border: none;
            color: white;
            padding: 5px 10px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            transition-duration: 0.4s;
            cursor: pointer;
            margin-right: 10px;
            border-radius: 5px;
        }

        #cancel-status:hover {
            background-color: #da190b;
            color: white;
        }



        #modal-status h2 {
            margin-bottom: 20px;
            color: #646566;
            font-size: 24px;
            margin: 0.67em 0;
            padding: 20px 0 20px 0;
            font-weight: bold;
        }

        #modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        #modal-expiration-date, #modal-create {
            width: 300px;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        #modal-expiration-date h2, , #modal-create h2 {
            margin-bottom: 20px;
        }

        #expiration-date-input {
            margin-bottom: 20px;
            width: 100%;
            padding: 5px;
        }

        #modal-date-buttons {
            display: flex;
            justify-content: center;
            width: 100%;
        }

        #save-expiration-date,
        #cancel-expiration-date {
            margin-top: 10px;
            padding: 5px 10px;
        }

        #save-expiration-date {
            background: #4CAF50; /* Green */
            border: none;
            color: white;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            transition-duration: 0.4s;
            cursor: pointer;
            border-radius: 5px;
        }

        #save-expiration-date:hover {
            background-color: #45a049;
            color: white;
        }

        #cancel-expiration-date {
            background-color: #f44336; /* Red */
            border: none;
            color: white;
            padding: 5px 10px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            transition-duration: 0.4s;
            cursor: pointer;
            margin-right: 10px;
            border-radius: 5px;
        }

        #cancel-expiration-date:hover {
            background-color: #da190b;
            color: white;
        }

        #modal-expiration-date h2 {
            margin-bottom: 20px;
            color: #646566;
            font-size: 24px;
            margin: 0.67em 0;
            padding: 20px 0 20px 0;
            font-weight: bold;
        }
        #modal-create h2 {
            margin-bottom: 20px;
            color: #646566;
            font-size: 24px;
            margin: 0.67em 0;
            padding: 20px 0 20px 0;
            font-weight: bold;
        }

        #modal-create label {
            display: block;
            margin-bottom: 5px;
            color: #64657e;
            font-size: 1rem;
        }

        #modal-create input[type=text],
        #modal-create input[type=number],
        #modal-create input[type=email],
        #modal-create input[type=tel] {
            margin-bottom: 20px;
            width: 100%;
            padding: 5px;
        }

        #modal-create-buttons {
            display: flex;
            justify-content: space-between;
            width: 100%;
        }

        #modal-create-buttons button {
            padding: 5px 10px;
        }

        #save-create {
            background: #4CAF50; /* Green */
            border: none;
            color: white;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            transition-duration: 0.4s;
            cursor: pointer;
            border-radius: 5px;
        }

        #save-create:hover {
            background-color: #45a049;
            color: white;
        }

        #cancel-create {
            background-color: #f44336; /* Red */
            border: none;
            color: white;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            transition-duration: 0.4s;
            cursor: pointer;
            border-radius: 5px;
        }

        #cancel-create:hover {
            background-color: #da190b;
            color: white;
        }

        #certificate-form{
            width: 222px;
        }
    </style>

    <?php
}