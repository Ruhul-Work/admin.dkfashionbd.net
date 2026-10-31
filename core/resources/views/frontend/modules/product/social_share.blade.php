<div class="quickview__social d-flex align-items-center mb-15">
    <label class="quickview__social--title">Social Share:</label>
    <ul class="quickview__social--wrapper mt-0 d-flex">
        <li class="quickview__social--list">
            <a class="quickview__social--icon color1" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(Request::url()) }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="7.667" height="16.524" viewBox="0 0 7.667 16.524">
                    <path data-name="Path 237" d="M967.495,353.678h-2.3v8.253h-3.437v-8.253H960.13V350.77h1.624v-1.888a4.087,4.087,0,0,1,.264-1.492,2.9,2.9,0,0,1,1.039-1.379,3.626,3.626,0,0,1,2.153-.6l2.549.019v2.833h-1.851a.732.732,0,0,0-.472.151.8.8,0,0,0-.246.642v1.719H967.8Z" transform="translate(-960.13 -345.407)" fill="currentColor"/>
                </svg>
                <span class="visually-hidden">Facebook</span>
            </a>
        </li>
        <li class="quickview__social--list">
            <a class="quickview__social--icon color2" target="_blank" href="https://twitter.com/intent/tweet?url={{ urlencode(Request::url()) }}">
                <i class="ri-twitter-x-fill"></i>
            </a>
        </li>
        <li class="quickview__social--list">
            <a class="quickview__social--icon" style="background-color:#00d772" target="_blank" href="https://wa.me/?text={{ urlencode(Request::url()) }}">
                <i class="ri-whatsapp-fill"></i>
                <span class="visually-hidden">WhatsApp</span>
            </a>
        </li>

        <li class="quickview__social--list">
            <a class="quickview__social--icon" style="background-color: #0077B5;" target="_blank" href="https://www.linkedin.com/shareArticle?url={{ urlencode(Request::url()) }}">
                <i class="ri-linkedin-fill"></i>
                <span class="visually-hidden">LinkedIn</span>
            </a>
        </li>







        {{--                                            <li class="quickview__social--list">--}}
        {{--                                                <a class="quickview__social--icon color3" target="_blank"--}}
        {{--                                                   href="https://www.instagram.com">--}}
        {{--                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16.497"--}}
        {{--                                                         height="16.492" viewBox="0 0 19.497 19.492">--}}
        {{--                                                        <path data-name="Icon awesome-instagram"--}}
        {{--                                                              d="M9.747,6.24a5,5,0,1,0,5,5A4.99,4.99,0,0,0,9.747,6.24Zm0,8.247A3.249,3.249,0,1,1,13,11.238a3.255,3.255,0,0,1-3.249,3.249Zm6.368-8.451A1.166,1.166,0,1,1,14.949,4.87,1.163,1.163,0,0,1,16.115,6.036Zm3.31,1.183A5.769,5.769,0,0,0,17.85,3.135,5.807,5.807,0,0,0,13.766,1.56c-1.609-.091-6.433-.091-8.042,0A5.8,5.8,0,0,0,1.64,3.13,5.788,5.788,0,0,0,.065,7.215c-.091,1.609-.091,6.433,0,8.042A5.769,5.769,0,0,0,1.64,19.341a5.814,5.814,0,0,0,4.084,1.575c1.609.091,6.433.091,8.042,0a5.769,5.769,0,0,0,4.084-1.575,5.807,5.807,0,0,0,1.575-4.084c.091-1.609.091-6.429,0-8.038Zm-2.079,9.765a3.289,3.289,0,0,1-1.853,1.853c-1.283.509-4.328.391-5.746.391S5.28,19.341,4,18.837a3.289,3.289,0,0,1-1.853-1.853c-.509-1.283-.391-4.328-.391-5.746s-.113-4.467.391-5.746A3.289,3.289,0,0,1,4,3.639c1.283-.509,4.328-.391,5.746-.391s4.467-.113,5.746.391a3.289,3.289,0,0,1,1.853,1.853c.509,1.283.391,4.328.391,5.746S17.855,15.705,17.346,16.984Z"--}}
        {{--                                                              transform="translate(0.004 -1.492)"--}}
        {{--                                                              fill="currentColor"/>--}}
        {{--                                                    </svg>--}}
        {{--                                                    <span class="visually-hidden">Instagram</span>--}}
        {{--                                                </a>--}}
        {{--                                            </li>--}}
        {{--                                            <li class="quickview__social--list">--}}
        {{--                                                <a class="quickview__social--icon color4" target="_blank"--}}
        {{--                                                   href="https://www.youtube.com">--}}
        {{--                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16.49"--}}
        {{--                                                         height="11.582" viewBox="0 0 16.49 11.582">--}}
        {{--                                                        <path data-name="Path 321"--}}
        {{--                                                              d="M967.759,1365.592q0,1.377-.019,1.717-.076,1.114-.151,1.622a3.981,3.981,0,0,1-.245.925,1.847,1.847,0,0,1-.453.717,2.171,2.171,0,0,1-1.151.6q-3.585.265-7.641.189-2.377-.038-3.387-.085a11.337,11.337,0,0,1-1.5-.142,2.206,2.206,0,0,1-1.113-.585,2.562,2.562,0,0,1-.528-1.037,3.523,3.523,0,0,1-.141-.585c-.032-.2-.06-.5-.085-.906a38.894,38.894,0,0,1,0-4.867l.113-.925a4.382,4.382,0,0,1,.208-.906,2.069,2.069,0,0,1,.491-.755,2.409,2.409,0,0,1,1.113-.566,19.2,19.2,0,0,1,2.292-.151q1.82-.056,3.953-.056t3.952.066q1.821.067,2.311.142a2.3,2.3,0,0,1,.726.283,1.865,1.865,0,0,1,.557.49,3.425,3.425,0,0,1,.434,1.019,5.72,5.72,0,0,1,.189,1.075q0,.095.057,1C967.752,1364.1,967.759,1364.677,967.759,1365.592Zm-7.6.925q1.49-.754,2.113-1.094l-4.434-2.339v4.66Q958.609,1367.311,960.156,1366.517Z"--}}
        {{--                                                              transform="translate(-951.269 -1359.8)"--}}
        {{--                                                              fill="currentColor"/>--}}
        {{--                                                    </svg>--}}
        {{--                                                    <span class="visually-hidden ">Youtube</span>--}}
        {{--                                                </a>--}}
        {{--                                            </li>--}}
    </ul>
</div>
