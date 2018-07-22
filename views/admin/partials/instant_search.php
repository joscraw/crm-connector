<!-- Add this to your HTML document -->
<script type="text/html" id="hit-template">
    <div class="panel panel-default">
        <div class="js-panel-heading student-list-panel-heading panel-heading">{{First Name}} {{Last Name}} {{Personal Email}}</div>
        <div class="js-panel-body panel-body student-list-panel-body table-responsive">
            <table class="table table-striped">
                <tr class="js-column-names">
                    <th>{{data_name_0}}</th>
                    <th>{{data_name_1}}</th>
                    <th>{{data_name_2}}</th>
                    <th>{{data_name_3}}</th>
                    <th>{{data_name_4}}</th>
                    <th>{{data_name_5}}</th>
                    <th>{{data_name_6}}</th>
                    <th>{{data_name_7}}</th>
                    <th>{{data_name_8}}</th>
                    <th>{{data_name_9}}</th>
                    <th>{{data_name_10}}</th>
                    <th>{{data_name_11}}</th>
                    <th>{{data_name_12}}</th>
                    <th>{{data_name_13}}</th>
                    <th>{{data_name_14}}</th>
                    <th>{{data_name_15}}</th>
                    <th>{{data_name_16}}</th>
                    <th>{{data_name_17}}</th>
                    <th>{{data_name_18}}</th>
                    <th>{{data_name_19}}</th>
                    <th>{{data_name_20}}</th>
                </tr>
                <tr class="js-column-values">
                    <td>{{data_value_0}}</td>
                    <td>{{data_value_1}}</td>
                    <td>{{data_value_2}}</td>
                    <td>{{data_value_3}}</td>
                    <td>{{data_value_4}}</td>
                    <td>{{data_value_5}}</td>
                    <td>{{data_value_6}}</td>
                    <td>{{data_value_7}}</td>
                    <td>{{data_value_8}}</td>
                    <td>{{data_value_9}}</td>
                    <td>{{data_value_10}}</td>
                    <td>{{data_value_11}}</td>
                    <td>{{data_value_12}}</td>
                    <td>{{data_value_13}}</td>
                    <td>{{data_value_14}}</td>
                    <td>{{data_value_15}}</td>
                    <td>{{data_value_16}}</td>
                    <td>{{data_value_17}}</td>
                    <td>{{data_value_18}}</td>
                    <td>{{data_value_19}}</td>
                    <td>{{data_value_20}}</td>
                </tr>
            </table>
        </div>
    </div>

</script>


<div class="row">
    <div class="col-md-12 js-nav-container">
        <button type="button" data-url="<?php echo admin_url('admin-ajax.php'); ?>" data-nonce="<?php echo wp_create_nonce( 'crmc_create_custom_export_nonce' ); ?>" class="btn btn-primary js-create-custom-export-button" data-toggle="modal">Create Custom Export</button>
        <hr>
    </div>
    <div class="col-md-12">
        <div class="js-AlgoliaInstantSearch" data-app-id="<?php echo get_option('crmc_algolia_application_id'); ?>" data-index-name="<?php echo get_option('crmc_algolia_index'); ?>" data-search-only-api-key="<?php echo get_option('crmc_algolia_search_only_api_key'); ?>">
            <header>
                <div>
                    <label for="searchAnything">Instant Search</label>
                    <input id="search-input" type="text" class="js-search-anything form-control"  placeholder="Search For A Student, Chapter, Email, etc">
                    <!-- We use a specific placeholder in the input to guides users in their search. -->
                </div>
            </header>
            <main>
                <div id="hits"></div>
                <div id="pagination"></div>
            </main>
        </div>
    </div>
</div>

<script>

    (function($) {

        this.$element = $('.js-AlgoliaInstantSearch');
        this.searchOnlyApiKey = this.$element.data('search-only-api-key');
        this.appId = this.$element.data('app-id');
        this.indexName = this.$element.data('index-name');

        window.search = instantsearch({
            // Replace with your own values
            appId: this.appId,
            apiKey: this.searchOnlyApiKey, // search only API key, no ADMIN key
            indexName: this.indexName,
            urlSync: false,
            searchParameters: {
                hitsPerPage: 10
            }
        });

        search.on('error', function (error) {
            var errorMessage = error.message.replace(/<\/?[^>]+(>|$)/g, "");
            errorMessage += " - Check to make sure the the Algolia API Settings are correct";
            this.$element.prepend(this.getError(errorMessage));
        }.bind(this));

        search.on('render', function (obj) {

            $('.js-column-names th').each(function(index, value){
                if($(value).html() === "")
                {
                    $(value).remove();
                }
            });

            $('.js-column-values td').each(function(index, value){
                if($(value).html() === "")
                {
                    $(value).remove();
                }
            });

        }.bind(this));

        search.addWidget(
            instantsearch.widgets.searchBox({
                container: '#search-input',
                wrapInput: true,
                autofocus: true,
                cssClasses: {
                    root: 'form-group',
                    input: 'form-control'
                }

            })
        );

        search.addWidget(
            instantsearch.widgets.hits({
                container: '#hits',
                transformData: function(obj)
                {
                    var keys_to_skip = ['objectID', '_highlightResult', '__hitIndex', 'chapter_id'];
                    var expected_keys = ['First Name', 'Last Name', 'Personal Email'];
                    var results_obj = {};
                    var i = 0;
                    for(var key in obj)
                    {
                        if(obj[key])
                        {
                            if(keys_to_skip.indexOf(key) !== -1)
                                continue;

                            if(expected_keys.indexOf(key) !== -1)
                                results_obj[key] = obj[key];

                            results_obj['data_name_' + i] = key;
                            results_obj['data_value_' + i] = obj[key];
                        }
                        i++;
                    }
                    return results_obj;
                },
                templates: {
                    item: document.getElementById('hit-template').innerHTML,
                    empty: "We didn't find any results for the search <em>\"{{query}}\"</em>"
                }
            })
        );

        search.addWidget(
            instantsearch.widgets.pagination({
                container: '#pagination'
            })
        );

        search.start();

        /**
         * Gets an html formated error message
         *
         * @param message
         * @return {string}
         */
        Window.prototype.getError = function (message) {
            debugger;
            var html = "<div class='alert alert-danger' role='alert'>";
            html += message;
            html += "</div>";
            return html;
        };

        $(document).on('click', '.js-panel-heading', function() {
            $(this).next('.js-panel-body').slideToggle( "slow", function() {});
        });

        $(document).on('click', '.js-create-custom-export-button', function() {

            debugger;
            var nonce = $(this).attr("data-nonce"),
                url = $(this).attr("data-url"),
                search_query = $('.js-search-anything').val();

            // perform ajax request
            jQuery.ajax({
                type : "post",
                dataType : "json",
                url : url,
                beforeSend: function() {
                    window.ajaxAttachLoadingDots(".js-create-custom-export-button");
                },
                data : {action: "crmc_create_custom_export", nonce: nonce, search_query: search_query}
            }).fail(function(r,status,jqXHR) {
                console.log('failed');
            }).done(function(response,status,jqXHR) {
                    window.ajaxRenderNoticesAndErrors(response, '.js-nav-container');
                    window.ajaxRemoveLoadingDots(".js-create-custom-export-button");
                });

        });

    })(jQuery);

</script>
