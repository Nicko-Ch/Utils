<?php

declare(strict_types=1);

namespace NickoCh\Utils\Tool;

use Composer\Autoload\ClassLoader;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Enum_;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Namespace_;

class ClassTool
{
    public static function findLoader(): ClassLoader
    {
        $loaders = spl_autoload_functions();

        foreach ($loaders as $loader) {
            if (is_array($loader) && $loader[0] instanceof ClassLoader) {
                return $loader[0];
            }
        }

        throw new \RuntimeException('Composer loader not found.');
    }

    public static function namespaceToPath(string $namespace): string
    {
        return strtr($namespace, [
            '\\' => '/'
        ]);
    }

    public static function pathToNamespace(string $path): string
    {
        return strtr($path, [
            '/' => '\\'
        ]);
    }

    public static function getNamespaceDirPathByPsr4(string $inputNamespace, ?ClassLoader $classLoader = null): array
    {
        $classLoader = $classLoader ?? self::findLoader();
        $autoloadPsr4 = $classLoader->getPrefixesPsr4();
        foreach ($autoloadPsr4 as $namespace => $path) {
            if (str_contains($inputNamespace, $namespace)) {
                return [true, $namespace, $path];
            }
        }
        return [false, null, null];
    }

    public static function getNamespaceDirPathByClassMap(string $classNameWithNamespace, ?ClassLoader $classLoader = null): string
    {
        $classLoader = $classLoader ?? self::findLoader();
        return $classLoader->getClassMap()[$classNameWithNamespace] ?? '';
    }

    public static function getFilePath(string $classNameWithNamespace, ?ClassLoader $classLoader = null): string
    {
        $classLoader = $classLoader ?? self::findLoader();
        $filePath = self::getNamespaceDirPathByClassMap($classNameWithNamespace, $classLoader);
        if (!$filePath) {
            [$isOk, $namespace, $dirPath] = self::getNamespaceDirPathByPsr4($classNameWithNamespace, $classLoader);
            if (!$isOk) {
                return '';
            }
            $psrFilePath = self::namespaceToPath(strtr($classNameWithNamespace, [
                $namespace => $dirPath[0].'/',
            ])).'.php';
            if (is_file($psrFilePath)) {
                $filePath = $psrFilePath;
            }
        }
        return $filePath;
    }

    public static function getClassNameByStmts(array $stmts, bool $withNamespace = true): string
    {
        $namespace = $className = '';
        foreach ($stmts as $stmt) {
            if ($stmt instanceof Namespace_ && $stmt->name) {
                $namespace = $stmt->name->toString();
                foreach ($stmt->stmts as $node) {
                    if (($node instanceof Class_ || $node instanceof Interface_ || $node instanceof Enum_) && $node->name) {
                        $className = $node->name->toString();
                        break;
                    }
                }
            }
        }
        $result = '';
        if ($className) {
            if ($namespace && $withNamespace) {
                $result .= $namespace . '\\';
            }
            $result .= $className;
        }
        return $result;
    }

    public static function leftTrimNamespace(string $namespace): string
    {
        return ltrim($namespace, '\\');
    }
}
