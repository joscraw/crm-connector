<?php

/** Loads the WordPress Environment and Template */
use CRMConnector\ContactMapper;
use CRMConnector\Crons\Initializers\BatchContactImportCronInitializer;
use CRMConnector\ContactTransformer;
use CRMConnector\Database\ContactSearch;
use CRMConnector\ExcelMapper;
use CRMConnector\Models\Collection;
use CRMConnector\StudentImportMapper;
use CRMConnector\Utils\Logger;
use PhpOffice\PhpSpreadsheet\Worksheet\RowIterator;
use CRMConnector\ReportGeneratorFactory;
use CRMConnector\Crons\Initializers\GenerateReportCronInitializer;

require( dirname( __FILE__ ) . '/../../../../wp/wp-load.php' );

global $wpdb;

$results = $wpdb->get_results(sprintf("SELECT id, report_id, report_type FROM %s%s WHERE status = '%s' AND failed_attempts <= 3 ORDER BY created_at DESC",
    $wpdb->prefix,
    'generate_report_cron',
    'IN_QUEUE'
));

foreach($results as $result) {

$cron_id = $result->id;
$report_id = $result->report_id;
$report_type = $result->report_type;
$logger = new Logger();
GenerateReportCronInitializer::set_log_file($cron_id, $logger);
GenerateReportCronInitializer::progress_cron($cron_id);


$factory = ReportGeneratorFactory::getInstance();
if(!$report = $factory->get($report_type)) {
    GenerateReportCronInitializer::fail_cron($cron_id);
    $logger->write(sprintf("Initializing Cron with id %s...", $cron_id));
}

try {
    $report->generate(false, true, $logger, $report_id);
} catch(\Exception $exception) {
    $logger->write(sprintf("Error on creating report. Exception: %s...", $exception->getMessage()));
}

$logger->write(sprintf("Finished Cron %s.", $cron_id));
GenerateReportCronInitializer::succeed_cron($cron_id);
}
