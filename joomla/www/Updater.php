<?php

class Updater
{
    private $src_path;
    private $revision;
    private $svn_repository;

    public function  getBuildPath() {
        return "{$this->src_path}";
    }

    public function __construct($path, $svn_repository = null, $revision = 'HEAD') {
        $this->src_path = $path;
        $this->revision = $revision;
        $this->svn_repository = $svn_repository;
    }

    public function checkout() {
        if ($this->svn_repository == null) {
            throw new Exception('You have to provide repository where checkout from.');
        }

        $path = $this->getBuildPath();
        $command = "svn co {$this->svn_repository} -r {$this->revision} \"$path\" 2>&1";
        return $this->exec($command);
    }

    public function update() {
        $path = $this->getBuildPath();
        $command = "svn update \"$path\" 2>&1";
        return $this->exec($command);
    }

    public function cleanup() {
        return $this->rrmdir($this->getBuildPath()) ? "{$this->getBuildPath()} removed." : "<span style='color:red'>{$this->getBuildPath()} was not removed.</span>";
    }

    private function rrmdir($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);

            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir."/".$object) == "dir") $this->rrmdir($dir."/".$object); else unlink($dir."/".$object);
                }
            }

            reset($objects);
            return rmdir($dir);
        }

        return false;
    }

    private function exec($command) {
        $output = "<strong>$command</strong><br />";

        ob_start();

        passthru($command, $result);

        $output .= ob_get_contents();

        ob_end_clean();

        return nl2br($output);
    }
}
