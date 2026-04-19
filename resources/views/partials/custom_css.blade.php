<style>
    .card-body {
        padding: 1.25rem !important;
        -webkit-box-flex: 1;
    }

    .stat-line {
        white-space: nowrap;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        line-height: 1.25;
        margin: 2px 0;
    }


    span.required {
        color: red;
    }

    td,
    th {
        text-align: center;
    }

    .pagination {
        flex-wrap: wrap;
    }

    .error-message {
        color: red;
    }


    /*
        Custom Radio Styling
*/

    @keyframes click-wave {
        0% {
            height: 40px;
            width: 40px;
            opacity: 0.35;
            position: relative;
        }

        100% {
            height: 110px;
            width: 110px;
            margin-left: -50px;
            margin-top: -50px;
            opacity: 0;
        }
    }

    .option-input {
        -webkit-appearance: none;
        -moz-appearance: none;
        -ms-appearance: none;
        -o-appearance: none;
        appearance: none;
        position: relative;
        top: 13.33333px;
        right: 0;
        bottom: 0;
        left: 0;
        height: 40px;
        width: 40px;
        transition: all 0.15s ease-out 0s;
        background: #cbd1d8;
        border: none;
        color: #fff;
        cursor: pointer;
        display: inline-block;
        margin-right: 0.5rem;
        outline: none;
        position: relative;
        z-index: 1000;
    }

    .option-input:hover {
        /* background: #9faab7; */
        background: #01D8DA;

    }

    .option-input:checked {
        /* background: #40e0d0;  */
        background: #01D8DA;

    }

    .option-input:checked::before {
        height: 40px;
        width: 40px;
        position: absolute;
        content: '✔';
        display: inline-block;
        font-size: 26.66667px;
        text-align: center;
        line-height: 40px;
    }

    .option-input:checked::after {
        -webkit-animation: click-wave 0.65s;
        -moz-animation: click-wave 0.65s;
        animation: click-wave 0.65s;
        background: #40e0d0;
        content: '';
        display: block;
        position: relative;
        z-index: 100;
    }

    .option-input.radio {
        border-radius: 50%;
    }

    .option-input.radio::after {
        border-radius: 50%;
    }


    .select2-selection__rendered {
        display: block !important;
    }

    /* This clears selected options from the list */

    .select2-results__option[aria-selected=true] {
        display: none;
    }


    .select2-container--open {
        z-index: 10000000000;
    }

    .file-preview-frame {
        width: 95%;
    }

    .app-sidebar .navigation li ul li a,
    .off-canvas-sidebar .navigation li ul li a {

        padding-left: 30px;
    }

    .tooltip {
        position: relative;
        display: inline-block;
    }

    .tooltip .tooltiptext {
        visibility: hidden;
        width: 140px;
        background-color: #555;
        color: #fff;
        text-align: center;
        border-radius: 6px;
        padding: 5px;
        position: absolute;
        z-index: 1;
        bottom: 150%;
        left: 50%;
        margin-left: -75px;
        opacity: 0;
        transition: opacity 0.3s;
    }

    .tooltip .tooltiptext::after {
        content: "";
        position: absolute;
        top: 100%;
        left: 50%;
        margin-left: -5px;
        border-width: 5px;
        border-style: solid;
        border-color: #555 transparent transparent transparent;
    }


    .tooltip:hover .tooltiptext {
        visibility: visible;
        opacity: 1;
    }

    .btn-xs {
        padding: .25rem .4rem;
        font-size: .875rem;
        line-height: .5;
        border-radius: .2rem;
    }

    .alert .close {
        font-size: 1em;
    }

    .bottom-hr {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
    }

    /* input[type="checkbox"]:checked {
  accent-color: green;
}
 */

    .oddcheck:checked {
        accent-color: green;
    }




    .smallercheckbox {
        transform: scale(0.7) !important;
        top: auto;
        margin-right: 0 !important;
        /* background: gray; */
        /* margin: 0 !important; */
        /* padding: 0 !important; */
    }

    .menu_p {

        max-height: 35px;
        display: flex;
        /* justify-content: center; */
        align-items: center;

        /* margin-top: 0 !important;
        margin: 0 !important;
        padding-top: 0 !important;
        padding: 0 !important;
        max-height: 35px;
        margin-bottom: margin-bottom: 5px; */

        /* max-height: 35px; */
        /* display: flex; */
        /* flex-direction: column; */
        /* justify-content: center; */
    }



    <?php if (isset($currentUser->darkmode) and $currentUser->darkmode) {
        ?> .dark {
        background: #3B3F51;
        color: wheat;
    }

    <?php
    }

    ?>
    /*=============== Extra Small Mobile Device ===============*/
    @media (max-width: 420px) {
        /* smartphones, iPhone, portrait 480x320 phones */
        @include('partials.css_mobile')
    }

    /*=============== Mobile Device ===============*/
    @media (max-width: 575px) {
        /* portrait e-readers (Nook/Kindle), smaller tablets @ 600 or @ 640 wide. */

        @include('partials.css_mobile')
    }

    /*=============== Small (sm) Device ===============*/
    @media (max-width: 767px) {
        /* portrait tablets, portrait iPad, landscape e-readers, landscape 800x480 or 854x480 phones */
        @include('partials.css_mobile')
    }


    /*=============== Medium (md) Device ===============*/
    @media (min-width: 768px) {
        @include('partials.css_desktop')
    }



    /* datatable pagination display fix for smaller screens  */
    @media screen and (max-width: 767px) {
        li.paginate_button.previous {
            display: inline;
        }

        li.paginate_button.next {
            display: inline;
        }

        li.paginate_button {
            display: none;
        }
    }
</style>
