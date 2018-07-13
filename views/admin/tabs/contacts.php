<!-- Add this to your HTML document -->
<script type="text/html" id="hit-template">
    <div class="panel panel-default">
        <div class="js-panel-heading student-list-panel-heading panel-heading">{{Student Prefix}} {{{_highlightResult.Student First Name.value}}} {{Student Middle Name}} {{{_highlightResult.Student Last Name.value}}} {{Student Suffix}}</div>
        <div class="js-panel-body panel-body student-list-panel-body table-responsive">
            <table class="table table-striped">
                <tr>
                    <th>Campus Address One</th>
                    <th>Campus Address Two</th>
                    <th>Campus City</th>
                    <th>Campus State</th>
                    <th>Campus Zip Code</th>
                    <th>Permanent Address One</th>
                    <th>Permanent Address Two</th>
                    <th>Permanent City</th>
                    <th>Permanent State</th>
                    <th>Permanent Zipcode</th>
                    <th>Student Permanent Phone Number</th>
                    <th>Student Email</th>
                    <th>Student Mobile Phone</th>
                    <th>GPA</th>
                </tr>
                <tr>
                    <td>{{{_highlightResult.Campus Address One.value}}}</td>
                    <td>{{{_highlightResult.Campus Address Two.value}}}</td>
                    <td>{{{_highlightResult.Campus City.value}}}</td>
                    <td>{{{_highlightResult.Campus State.value}}}</td>
                    <td>{{{_highlightResult.Campus Zip Code.value}}}</td>
                    <td>{{{_highlightResult.Permanent Address One.value}}}</td>
                    <td>{{{_highlightResult.Permanent Address Two.value}}}</td>
                    <td>{{{_highlightResult.Permanent City.value}}}</td>
                    <td>{{{_highlightResult.Permanent State.value}}}</td>
                    <td>{{{_highlightResultPermanent Zipcode.value}}}</td>
                    <td>{{{_highlightResult.Student Permanent Phone Number.value}}}</td>
                    <td>{{{_highlightResult.Student Email.value}}}</td>
                    <td>{{{_highlightResult.Student Mobile Phone.value}}}</td>
                    <td>{{GPA}}</td>
                </tr>
            </table>
        </div>
    </div>

</script>

<div class="row">
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

    var search = instantsearch({
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
        debugger;
        var errorMessage = error.message.replace(/<\/?[^>]+(>|$)/g, "");
        errorMessage += " - Check to make sure the the Algolia API Settings are correct";
        this.$element.prepend(this.getError(errorMessage));
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

})(jQuery);

</script>
