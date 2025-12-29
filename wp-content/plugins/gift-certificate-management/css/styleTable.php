<?php
function getStyleTable() {
    ?>
    <style type="text/css">
        table {
            width: calc(100% - 20px);
            /*   max-width: 600px; */
            /*height: 320px;*/
            border-collapse: collapse;
            border: 1px solid #38678f;
            background: #E9E4E1;
        }
        .heavyTable {
            box-shadow: none;
            animation: float 5s infinite;
        }
        th {
            background: #F1B5B0;
            height: 54px;
            font-weight: lighter;
            text-shadow: 0 1px 0 #E69393;
            color: white;
            border: 1px solid #E69393;
            box-shadow: inset 0px 1px 2px #E69393;
            transition: all 0.2s;
            text-align: center;
            font-weight: bold;
            font-size: 14px;
        }
        tr {
            border-bottom: 1px solid #3f622d;
        }
        tr:last-child {
            border-bottom: 0px;
        }
        td {
            border-right: 1px solid #3f622d;
            padding: 10px;
            transition: all 0.2s;
            background-color: #fff;
            color: #3c434a;
            font-weight: bold;
            font-size: 13px;
        }
        .editable{
            background-color: #ffffff;
        }
        td:first-child, td:nth-child(1){
            text-align: center;
        };
        td:last-child {
            border-right: 0px;
            text-align: right;
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
        .heavyTable {
            box-shadow: 0 0 20px 0 rgb(0 0 0 / 20%), 0 5px 5px 0 rgb(0 0 0 / 24%);
            animation: float 5s infinite;
        }
        .main .heavyTable {
            width: 650px;
            box-shadow: none;
        }
        .main {
            max-width: 100%;
            padding: 10px;
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

        h1 {
            text-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        h2 {
            text-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
            color: #ffffff;
        }
        p,input{
            color: #ffffff;
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
            position: absolute;
            width: 650px;
            z-index: 1000;
        }
    </style>

    <?php
}