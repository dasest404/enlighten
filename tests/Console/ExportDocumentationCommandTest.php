<?php

namespace Tests\Console;

use Mockery;
use Styde\Enlighten\Console\Commands\ExportDocumentationCommand;
use Styde\Enlighten\Console\DocumentationExporter;
use Tests\TestCase;

class ExportDocumentationCommandTest extends TestCase
{
    private $exporterSpy;

    protected function setUp(): void
    {
        parent::setUp();

        $command = new ExportDocumentationCommand(
            $this->exporterSpy = Mockery::spy(DocumentationExporter::class)
        );

        $this->app->instance(ExportDocumentationCommand::class, $command);
    }

    /** @test */
    function exports_a_run()
    {
        $this->createRun('main', 'abcde', true);
        $this->createRun('develop', 'fghij', false);

        $selectedRun = 'main * abcde';

        $this->artisan('enlighten:export')
            ->expectsChoice("Please select the run you'd like to export", $selectedRun, [
                'develop fghij',
                $selectedRun
            ])
            ->expectsQuestion('In which directory would you like to export the documentation?', 'public/docs')
            ->expectsQuestion("What's the base URL for this documentation going to be?", 'docs')
            ->expectsOutput('`main * abcde` run exported!')
            ->assertExitCode(0);

        $this->exporterSpy->shouldHaveReceived('export', function ($run, $baseDir, $baseUrl) use ($selectedRun) {
            $this->assertSame($selectedRun, $run->signature);
            $this->assertSame('public/docs', $baseDir);
            $this->assertSame('/docs', $baseUrl);

            return true;
        });
    }

    /** @test */
    function asks_the_user_to_run_the_tests_before_trying_to_export_the_documentation()
    {
        $this->artisan('enlighten:export')
            ->expectsOutput('There are no runs available. Please setup `Enlighten` and run the tests first.');
    }
}
