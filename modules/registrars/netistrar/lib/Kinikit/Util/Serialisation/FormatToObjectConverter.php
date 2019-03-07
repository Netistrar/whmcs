<?php

namespace Kinikit\Core\Util\Serialisation;

/**
 * Interface implemented by specific format to object converters.  This enables the use of a generic service dispatcher
 *
 * @author mark
 *
 */
interface FormatToObjectConverter {

    /**
     * Only required method.  Should convert whatever it receives into native PHP objects / arrays or primitives.
     *
     * @param mixed $formatString
     */
    public function convert($formatString);

}

?>