<!DOCTYPE html>
<html>
<head>
    <title>@yield('title')</title>
    <style type="text/css">
        .error_site {
            background: #051c2a;
            color: #fff;
            text-transform: uppercase;
            font-family: arial;
        }

        .error_site .row {
            position: absolute;
            top: 25%;
            left: 25%;
            right: 25%;
            bottom: 25%;
        }

        .error_site img {
            margin: auto;
            display: block;
        }

        .error_site h1 {
            font-weight: bolder;
        }

        .error_site h3.ftr {
            margin-top: 10px;
        }
    </style>
</head>
<body class="error_site">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
                <h1>
                    <svg 
                         xmlns="http://www.w3.org/2000/svg"
                         xmlns:xlink="http://www.w3.org/1999/xlink"
                         width="26px" height="26px">
                        <defs>
                        <filter id="Filter_0">
                            <feFlood flood-color="rgb(255, 255, 255)" flood-opacity="1" result="floodOut" />
                            <feComposite operator="atop" in="floodOut" in2="SourceGraphic" result="compOut" />
                            <feBlend mode="normal" in="compOut" in2="SourceGraphic" />
                        </filter>

                        </defs>
                        <g filter="url(#Filter_0)">
                        <path fill-rule="evenodd"  fill="rgb(255, 255, 255)"
                         d="M12.997,0.013 C5.827,0.013 0.013,5.827 0.013,12.997 C0.013,20.168 5.827,25.981 12.997,25.981 C20.168,25.981 25.981,20.168 25.981,12.997 C25.981,5.827 20.168,0.013 12.997,0.013 ZM15.700,20.136 C15.032,20.399 14.500,20.600 14.100,20.739 C13.703,20.877 13.240,20.947 12.714,20.947 C11.904,20.947 11.275,20.747 10.826,20.354 C10.378,19.959 10.154,19.459 10.154,18.852 C10.154,18.615 10.171,18.373 10.204,18.127 C10.238,17.881 10.292,17.604 10.365,17.293 L11.202,14.339 C11.276,14.053 11.340,13.784 11.390,13.533 C11.440,13.280 11.465,13.048 11.465,12.838 C11.465,12.462 11.387,12.198 11.232,12.050 C11.074,11.901 10.779,11.828 10.338,11.828 C10.122,11.828 9.901,11.860 9.673,11.927 C9.448,11.996 9.252,12.059 9.091,12.121 L9.312,11.211 C9.860,10.987 10.384,10.796 10.884,10.638 C11.385,10.478 11.857,10.398 12.302,10.398 C13.106,10.398 13.726,10.594 14.163,10.981 C14.596,11.369 14.815,11.874 14.815,12.493 C14.815,12.622 14.800,12.849 14.770,13.172 C14.740,13.496 14.684,13.791 14.603,14.064 L13.771,17.009 C13.703,17.247 13.642,17.516 13.587,17.818 C13.533,18.120 13.507,18.351 13.507,18.508 C13.507,18.898 13.594,19.165 13.770,19.308 C13.944,19.449 14.249,19.521 14.678,19.521 C14.882,19.521 15.110,19.485 15.367,19.413 C15.623,19.343 15.807,19.280 15.923,19.226 L15.700,20.136 ZM15.553,8.179 C15.165,8.539 14.697,8.720 14.151,8.720 C13.607,8.720 13.135,8.539 12.744,8.179 C12.355,7.819 12.158,7.380 12.158,6.868 C12.158,6.357 12.356,5.917 12.744,5.553 C13.135,5.188 13.607,5.007 14.151,5.007 C14.697,5.007 15.165,5.188 15.553,5.553 C15.940,5.917 16.136,6.357 16.136,6.868 C16.136,7.381 15.940,7.819 15.553,8.179 Z"/>
                        </g>
                        </svg>
                    INFORMATION
                </h1>
                <h3>@yield('message')</h3>
            </div>
        </div>
    </div>
</body>
</html>

<script type="text/javascript">
    setTimeout(function() {
        window.close();
    }, 10000);
</script>