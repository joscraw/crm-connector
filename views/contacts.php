<div class="container">

    <?php
    include('partials/nav.php');
    ?>

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

            <div data-controller="AlgoliaInstantSearch" data-app-id="<?php echo get_option('crmc_algolia_application_id'); ?>" data-index-name="<?php echo get_option('crmc_algolia_index'); ?>" data-search-only-api-key="<?php echo get_option('crmc_algolia_search_only_api_key'); ?>">
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
</div>