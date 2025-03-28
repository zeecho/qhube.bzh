<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Yaml\Yaml;

class TranslationDataGenerator {
    private ParameterBagInterface $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    public function generateData($projectKey, $projectInfo, $language)
    {
        $basePath = $this->params->get('kernel.project_dir') . '/data/translation_sources/';

        $data = [];

        switch ($projectInfo['format']) {
            case 'yml':
                $file = $basePath . $projectKey . '/' . $language . '.yml';
                if (file_exists($file)) {
                    $data = Yaml::parseFile($file);
                }

                break;
            case 'tsv':
                $projectPath = $basePath . $projectKey . '/';
                $filesInDir = array_diff(scandir($projectPath), array('.', '..'));
                foreach ($filesInDir as $fileName) {
                    if (!is_dir($fileName) && preg_match('/\.tsv$/', $fileName)) {
                        $fileName = $basePath . $projectKey . '/' . $fileName;
                        $baseFileName = pathinfo($fileName, PATHINFO_FILENAME);
                        $tsvArray = $this->tsv_to_array($fileName);
                        if (array_key_exists($language, $tsvArray)) {
                            $data[$language][$baseFileName] = $tsvArray[$language];
                        }
                    }
                }
                break;
        }

        return $data;
    }

    // TSV to Array Function based on https://inkplant.com/code/csv-to-array for the BOM part
    private function tsv_to_array(string $file)
    {
        $data = array();

        if (($handle = fopen($file,'r')) !== false) {
            // check for BOM header at the beginning of the file
            // see https://stackoverflow.com/questions/29828508/fgetcsv-wrongly-adds-double-quotes-to-first-element-of-first-line
            $bom = "\xef\xbb\xbf";
            if (fgets($handle, 4) !== $bom) {
                // the BOM was not found; rewind pointer to start of file
                rewind($handle);
            }

            $c = 0;
            $headers = [];
            $data = [];
            while (!feof($handle)) {
                $line = fgetcsv($handle, 10240, "\t", '"', '\\');
                if ($c == 0) {
                    $headers = $line;
                } else {
                    if ($line) {
                        $autoKey = $headers[0] !== 'key';
                        $key = $autoKey ? 'AUTOGEN-KEY-' . $c : $line[0];
                        foreach ($line as $index => $item) {
                            if ($autoKey || $index > 0) {
                                $data[$headers[$index]][$key] = $item;
                            }
                        }
                    }
                }
                $c++;
            }
            fclose($handle);
        }

        return $data;
    }
}