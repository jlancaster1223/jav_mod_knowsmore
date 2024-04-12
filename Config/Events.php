<?php

namespace Module\Knowsmore\Config;

use App\Libraries\Admin\Permissions;
use App\Libraries\System\Events;

// =================================================================================================
// TODO LIST
// > Return suggestions
// =================================================================================================

Events::on('inject_foot', function () {
?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Ondocument load, console.log('loaded')
        $(document).ready(function() {
            // Load the modal 
            $('header.relative').append(`
            <div class="knowsmore">
                <div class="container">
                    <!-- <div class="knowsmore_column1 suggestions">
                        <h6>Suggestions</h6>
                        <div class="results"></div>
                    </div> -->
                    <div class="knowsmore_column1 categories">
                        <h6>Categories</h6>
                        <div class="results"></div>
                    </div>
                    <div class="knowsmore_column4 products">
                        <h6>Products</h6>
                        <div class="results"></div>
                        <a href="/search?q=">View all</a>
                    </div>
                </div>
            </div>
            `);
        });

        // On keyup of inputting into header .searchbox name=q
        $('header.relative .searchbox input[name=q]').on('keyup', function() {
            // Cancel any previous requests
            if (window.knowsmore_request) {
                window.knowsmore_request.abort();
            }

            // If the query is empty, hide the modal
            if ($(this).val() == '') {
                $('.mini-cart-overlay').removeClass('active');
                $('.knowsmore').removeClass('active');
                $('header.relative').css('z-index', 0);
                $('header.relative').css('background', 'transparent');
                return;
            }

            // Post the value of the input to a controller
            window.knowsmore_request = $.post('/knowsmore/search', {
                q: $(this).val()
            }, function(response) {
                // Parse the response
                $('.mini-cart-overlay').addClass('active');
                $('.knowsmore').addClass('active');

                // Set z-index of header to 101
                $('header.relative').css('z-index', 101);
                $('header.relative').css('background', 'white');
                response = JSON.parse(response);
                // Update the modal
                // $('.knowsmore_column1.suggestions .results').html(response.suggestions);
                $('.knowsmore_column1.categories .results').html(response.categories);
                $('.knowsmore_column4.products .results').html(response.products);

                // Set the href of the view all link
                $('.knowsmore_column4.products a').attr('href', '/search?q=' + $(this).val());
            });
        });
    </script>

    <style>
        @media screen and (min-width: 700px) {
            .knowsmore.active {
                display: block;
            }

            .knowsmore .container {
                display: flex;
                flex-direction: row;
                gap: 32px;
            }

            .knowsmore_column1 {
                flex: 1;
            }

            .knowsmore_column1 .results {
                display: flex;
                flex-direction: column;
                gap: 16px;
            }

            .knowsmore_column4 {
                flex: 4;
            }

            .knowsmore h6 {
                margin-bottom: 16px;
            }

            /* Set .products__item__image to be aspect ratio 1 */
            .knowsmore .products .results {
                display: flex;
                flex-direction: row;
                gap: 32px;
            }

            .knowsmore .products>a {
                margin-top: 24px;
                padding-top: 24px;
                border-top: 1px solid #C9CBCC;
                display: block;
                text-align: center;
            }

            .knowsmore .products__item {
                flex: 1;
            }

            .knowsmore .products__item__image {
                padding-top: 100%;
                position: relative;
                overflow: hidden;
            }

            .knowsmore .products__item__image img {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                object-fit: cover;
            }
        }

        .knowsmore {
            display: none;
            position: absolute;
            width: 100%;
            z-index: 101;
            background: white;
            padding: 36px 0;
        }
    </style>
<?php
});
