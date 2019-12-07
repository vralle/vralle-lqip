<?php
/**
 * Imagick Image Editor
 *
 * @package vralle-lqip
 */

namespace VRalleLqip;

use function base64_encode;
use function floor;
use function substr;
use function strpos;
use WP_Image_Editor_Imagick;
use WP_Error;

/**
 * Image Editor Class for Image Manipulation through Imagick PHP Module
 */
class Gaussholder_Image_Editor_Imagick extends WP_Image_Editor_Imagick {
    /**
     * Generate LQIP Parts
     *
     * @param  int $sample_factor Scaling factor.
     * @return mixed Preview image parts or null.
     */
    public function create_lqip( $sample_factor ) {
        $lqip_part = null;
        try {
            $width  = floor( $this->size['width'] / $sample_factor );
            $height = floor( $this->size['height'] / $sample_factor );
            // Normalise the density to 72dpi.
            $this->image->setImageResolution( 72, 72 );
            // Set sampling factors to constant.
            $this->image->setSamplingFactors( array( '1x1', '1x1', '1x1' ) );
            // Ensure we use default Huffman tables.
            $this->image->setOption( 'jpeg:optimize-coding', false );
            // Strip unnecessary header data.
            $this->image->stripImage();
            $this->image->scaleImage( $width, $height );
            $scaled = $this->image->getImageBlob();
            // Strip the header.
            $lqip_part = substr( $scaled, strpos( $scaled, "\xFF\xDA" ) + 2 );
        } catch ( Exception $e ) {
            new WP_Error( 'create_lqip_error', $e->getMessage(), $this->file );
        }

        if ( $lqip_part ) {
            $lqip_part = base64_encode( $lqip_part ) . ',' . $width . ',' . $height;
        }

        return $lqip_part;
    }
}
