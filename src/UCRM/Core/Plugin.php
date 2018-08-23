<?php
declare(strict_types=1);

namespace UCRM\Core;

/**
 * Class Bundler
 *
 * @package UCRM\Core
 */
final class Plugin
{
    // =================================================================================================================
    // CONSTANTS
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @const string The default project base path, when following the folder structure in <b>ucrm-plugin-template.</b>
     */
    private const DEFAULT_PLUGIN_PATH =
        __DIR__.
        DIRECTORY_SEPARATOR."..".
        DIRECTORY_SEPARATOR."..".
        DIRECTORY_SEPARATOR."..".
        DIRECTORY_SEPARATOR."..".
        DIRECTORY_SEPARATOR."..".
        DIRECTORY_SEPARATOR."..".
        DIRECTORY_SEPARATOR."..".
        DIRECTORY_SEPARATOR;

    /**
     * @const string The default .zipignore file path, in the root of the project, including filename.
     */
    private const DEFAULT_IGNORE_PATH =
        self::DEFAULT_PLUGIN_PATH.
        ".zipignore";

    // =================================================================================================================
    // PRIVATE METHODS
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Bundler::inIgnoreFile()
     * Checks an optional .zipignore file for inclusion of the specified string.
     *
     * @param string $path The relative path for which to search in the ignore file.
     * @param string $ignore The path to the optional ignore file, defaults to project root.
     *
     * @return bool Returns true if the path is found in the file, otherwise false.
     */
    private static function inIgnoreFile(string $path, string $ignore = ""): bool
    {
        $ignore = $ignore ?: realpath(self::DEFAULT_IGNORE_PATH);

        if (!file_exists($ignore)) {
            return false;
        }

        $lines = explode(PHP_EOL, file_get_contents($ignore));

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line === "")
                continue;

            $starts_with = substr($line, 0, 1) !== "#" && substr($path, 0, strlen($line)) === $line;

            if ($starts_with)
                return true;
        }

        return false;
    }

    // =================================================================================================================
    // PUBLIC METHODS
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Bundler::bundle()
     * Creates a zip archive for use as an UCRM Plugin.
     *
     * @param string $root Path to root of the project.
     * @param string $ignore Path to the optional .zipignore file.
     */
    public static function bundle(string $root = "", string $ignore = ""): void
    {
        echo "Bundling...\n";

        $root = realpath($root ?: self::DEFAULT_PLUGIN_PATH);
        $ignore = realpath($ignore ?: self::DEFAULT_IGNORE_PATH);

        $archive_name = basename($root);
        $archive_path = $root.DIRECTORY_SEPARATOR."zip".DIRECTORY_SEPARATOR;

        echo "$archive_path => $archive_name.zip\n";

        $directory = new \RecursiveDirectoryIterator($archive_path);
        $file_info = new \RecursiveIteratorIterator($directory);

        $files = [];
        foreach ($file_info as $info)
        {
            $real_path = $info->getPathname();
            $file_name = $info->getFilename();

            // Skip /. and /..
            if($file_name === "." || $file_name === "..")
                continue;

            $path = str_replace($root, "", $real_path); // Remove base path from the path string.
            $path = str_replace(DIRECTORY_SEPARATOR, "/", $path); // Match .zipignore format
            $path = substr($path, 1, strlen($path) - 1);

            if (!self::inIgnoreFile($path, $ignore))
            {
                $files[] = $path;
                echo "ADDED  : $path\n";
            }
            else
                echo "IGNORED: $path\n";
        }

        $zip = new \ZipArchive();
        $file_name = $root.DIRECTORY_SEPARATOR."$archive_name.zip";

        if ($zip->open($file_name, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            exit("Unable to create $file_name!\n");
        }

        foreach ($files as $file) {
            // Ensure .zipignore directory separators are converted to OS separators.
            $path = str_replace("/", DIRECTORY_SEPARATOR, $file);

            // Remove the leading folder, as we do not want that structure in the ZIP archive.
            $local = str_replace("zip" . DIRECTORY_SEPARATOR, "", $path);

            // Add the file to the archive.
            $zip->addFile($path, $local);
        }

        $total_files = $zip->numFiles;
        $status = $zip->status !== 0 ? $zip->getStatusString() : "SUCCESS!";

        echo "FILES  : $total_files\n";
        echo "STATUS : $status\n";

        // Close the archive, we're all finished!
        $zip->close();
    }

}