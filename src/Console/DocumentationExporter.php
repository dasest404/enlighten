<?php

namespace Styde\Enlighten\Console;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Routing\UrlGenerator;
use Styde\Enlighten\Models\Example;
use Styde\Enlighten\Models\Run;

class DocumentationExporter
{
    /**
     * @var Filesystem
     */
    private $filesystem;
    /**
     * @var string
     */
    private $baseDir;

    /**
     * @var ContentRequest
     */
    private $request;

    /**
     * @var string
     */
    protected $currentBaseUrl;

    /**
     * @var string
     */
    protected $runBaseUrl;

    public function __construct(Filesystem $filesystem, string $baseDir, ContentRequest $request, string $currentBaseUrl)
    {
        $this->filesystem = $filesystem;
        $this->baseDir = $baseDir;
        $this->request = $request;
        $this->currentBaseUrl = $currentBaseUrl;
    }

    public function export(Run $run)
    {
        $this->runBaseUrl = "{$this->currentBaseUrl}/enlighten/run/{$run->id}";

        $this->createDirectory('/');

        $this->createFile('index.html', $this->withContentFrom($run->url));

        $run->groups->each(function ($group) {
            $this->exportGroupWithExamples($group);
        });
    }

    private function exportGroupWithExamples($group)
    {
        $this->createFile("{$group->slug}.html", $this->withContentFrom($group->url));

        $this->createDirectory($group->slug);

        $group->examples->each(function (Example $example) use ($group) {
            $this->exportExample($example->setRelation('group', $group));
        });
    }

    private function exportExample($example)
    {
        $this->createFile(
            "{$example->group->slug}/{$example->method_name}.html",
            $this->withContentFrom($example->url)
        );
    }

    private function createDirectory($path)
    {
        if ($this->filesystem->isDirectory("{$this->baseDir}/$path")) {
            return;
        }

        $this->filesystem->makeDirectory("{$this->baseDir}/$path", 0755, true);
    }

    private function createFile(string $filename, string $contents)
    {
        $this->filesystem->put("{$this->baseDir}/{$filename}", $contents);
    }

    private function withContentFrom(string $url): string
    {
        return preg_replace_callback(
            "@{$this->runBaseUrl}/([^\"]+)@",
            function ($matches) {
                return $this->getStaticUrl($matches[0]);
            },
            $this->request->getContent($url)
        );
    }

    private function getStaticUrl(string $originalUrl): string
    {
        // http://127.0.0.1:8000/

        $result = str_replace($this->runBaseUrl, '/docs', $originalUrl);

        $result .= '.html';

        return $result;
    }
}
