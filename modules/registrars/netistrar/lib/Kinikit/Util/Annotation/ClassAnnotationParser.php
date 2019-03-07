<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 16/08/2018
 * Time: 12:58
 */

namespace Kinikit\Core\Util\Annotation;


class ClassAnnotationParser {

    private static $instance;

    /**
     * Only allow private construction.
     *
     * ClassAnnotation constructor.
     * @param $className
     */
    private function __construct() {
    }


    /**
     * Parse annotations for a class
     */
    public function parse($className) {


        try {
            $class = new \ReflectionClass($className);
        } catch (\ReflectionException $e) {
            return new ClassAnnotations($className, array(), array(), array());
        }


        // get the master annotations
        $classComment = $class->getDocComment();
        $classAnnotations = $this->getCommentAnnotations($classComment);
        $classAnnotations["comment"] = array(new Annotation("comment", $this->cleanComment($classComment)));


        $fieldAnnotations = array();
        $properties = $class->getProperties();

        foreach ($properties as $property) {

            if ($property->isStatic())
                continue;


            $fieldComment = $this->getCommentAnnotations($property->getDocComment());
            $fieldAnnotations[$property->getName()] = $fieldComment;

            $fieldAnnotations[$property->getName()]["comment"] = array(new Annotation("comment", $this->cleanComment($property->getDocComment())));

        }


        $methodAnnotations = array();
        $methods = $class->getMethods();

        foreach ($methods as $method) {

            if ($method->isStatic() || $method->isPrivate() || $method->isProtected())
                continue;
            $methodComment = $this->getCommentAnnotations($method->getDocComment());
            $methodAnnotations[$method->getName()] = $methodComment;

            if ($method->getDeclaringClass()->getName() != $className) {
                $methodAnnotations[$method->getName()]["inherited"] = 1;
            }

            $methodAnnotations[$method->getName()]["comment"] = array(new Annotation("comment", $this->cleanComment($method->getDocComment())));


        }


        return new ClassAnnotations($className, $classAnnotations, $fieldAnnotations, $methodAnnotations);

    }


    private function getCommentAnnotations($comment) {

        $cleaned = str_replace(array("*/", "/*", "\n/"), array("", "", "", ""), $comment);
        $annotations = array();
        preg_replace_callback("/@([a-zA-Z-]*)(.*)/", function ($matches) use (&$annotations) {
            if (!isset($annotations[$matches[1]]))
                $annotations[$matches[1]] = array();

            $annotations[$matches[1]][] = new Annotation($matches[1], trim($matches[2]));
            return "";
        }, $cleaned);

        return $annotations;
    }


    // Clean a comment
    private function cleanComment($comment) {
        $commentLines = explode(PHP_EOL, $comment);
        for ($i = sizeof($commentLines) - 1; $i >= 0; $i--) {
            $commentLine = $commentLines[$i];
            if (is_numeric(strpos($commentLine, "/**")) ||
                is_numeric(strpos($commentLine, "*/")) ||
                preg_match("/@[\s\S]*$/",$commentLine) ||
                !trim($commentLine,"* ")) {
                array_splice($commentLines, $i, 1);
            }
        }

        return join(PHP_EOL, $commentLines);
    }

    /**
     * Return singleton instance
     */
    public static function instance() {
        if (self::$instance == null) {
            self::$instance = new ClassAnnotationParser();
        }

        return self::$instance;
    }


}