import { canvasRGB } from 'stackblur-canvas';

const lqipHeader = window.GaussholderHeader;

const lqip = function( img, lqipData ) {
  let lqipImg;
  let canvas;
  let ctx;

  const createLqip = function() {
    const arrayBufferToBase64 = function( buffer ) {
      let binary = '';
      const bytes = new Uint8Array( buffer );
      const len = bytes.byteLength;
      for ( let i = 0; i < len; i++ ) {
        binary += String.fromCharCode( bytes[ i ] );
      }
      return window.btoa( binary );
    };

    const reconstituteImage = function() {
      const lqipWidth = parseInt( lqipData[ 1 ] );
      const lqipHeight = parseInt( lqipData[ 2 ] );
      const lqipPart = lqipData[ 0 ];
      const lqipTotal = atob( lqipHeader.header ) + atob( lqipPart );
      const bytes = new Uint8Array( lqipTotal.length );
      for ( let i = 0; i < lqipTotal.length; i++ ) {
        bytes[ i ] = lqipTotal.charCodeAt( i );
      }

      // Poke the bits.
      /* eslint-disable no-bitwise */
      bytes[ lqipHeader.height_offset ] = ( ( lqipHeight >> 8 ) & 0xFF );
      bytes[ lqipHeader.height_offset + 1 ] = ( lqipHeight & 0xFF );
      bytes[ lqipHeader.length_offset ] = ( ( lqipWidth >> 8 ) & 0xFF );
      bytes[ lqipHeader.length_offset + 1 ] = ( lqipWidth & 0xFF );
      /* eslint-enable no-bitwise */

      // Back to a full JPEG now.
      return arrayBufferToBase64( bytes );
    };

    const onloadLqip = function() {
      // Render only in viewport.
      if ( lqipImg ) {
        lqipImg.removeEventListener( 'load', onloadLqip );
        lqipImg.removeEventListener( 'error', onloadLqip );

        ctx.drawImage( lqipImg, 0, 0, canvasWidth, canvasHeight );
        canvasRGB( canvas, 0, 0, canvasWidth, canvasHeight, 1 );

        // Use modern toBlob. toDataURL has performance issues.
        if ( canvas.toBlob ) {
          canvas.toBlob( function( blob ) {
            const url = window.URL.createObjectURL( blob );
            img.style.backgroundImage = 'url("' + url + '")';
            // window.URL.revokeObjectURL( url );
          } );
        } else {
          img.style.backgroundImage = 'url("' + canvas.toDataURL() + '")';
        }
        img.style.backgroundRepeat = 'no-repeat';
        img.style.backgroundSize = 'cover';
      }
    };

    // Use original LQIP sizes and background-size=cover for performance reasons.
    // Blur Radius 1 for small images.
    // We do not use full size image. Blur Images over 100 * 100px can cause
    // big performance issues even on desktop devices.
    const canvasWidth = parseInt( lqipData[ 1 ], 10 );
    const canvasHeight = parseInt( lqipData[ 2 ], 10 );

    canvas = document.createElement( 'canvas' );
    canvas.width = canvasWidth;
    canvas.height = canvasHeight;
    ctx = canvas.getContext( '2d', { alpha: false } );
    // Ensure smoothing is off
    ctx.mozImageSmoothingEnabled = false;
    ctx.webkitImageSmoothingEnabled = false;
    ctx.msImageSmoothingEnabled = false;
    ctx.imageSmoothingEnabled = false;

    lqipImg = new Image();
    lqipImg.addEventListener( 'load', onloadLqip );
    lqipImg.addEventListener( 'error', onloadLqip );
    lqipImg.src = 'data:image/jpg;base64,' + reconstituteImage();
  };

  const remove = function() {
    if ( lqipImg ) {
      img.style.backgroundImage = '';
      img.style.backgroundRepeat = '';
      img.style.backgroundSize = '';
      lqipImg = null;
      ctx = null;
      canvas = null;
    }
    if ( img.style.length == 0 ) {
      img.removeAttribute( 'style' );
    }
  };

  const lazyloaded = function() {
    img.removeEventListener( 'lazyloaded', lazyloaded );
    img.style.opacity = 0;
    img.style.webkitClipPath = 'inset(0)'; // WebKit
    img.style.clipPath = 'inset(0)'; // Standard

    const fadeDuration = 5000;
    let start = 0;
    function fade( ts ) {
      if ( ! start ) {
        start = ts;
      }
      const diff = ts - start;

      if ( diff > fadeDuration ) {
        img.style.opacity = '';
        img.style.clipPath = '';
        img.style.webkitClipPath = '';
        return;
      }
      const opacity = diff / fadeDuration;

      img.style.opacity = opacity;

      window.requestAnimationFrame( fade );
    }

    window.requestAnimationFrame( fade );

    setTimeout( remove, 5000 );
  };

  createLqip();

  // Render original image only in viewport.
  img.setAttribute( 'data-expand', -1 );
  img.addEventListener( 'lazyloaded', lazyloaded );
};

window.addEventListener( 'lazybeforeunveil', function( e ) {
  const img = e.target;
  const lqipData = img.getAttribute( 'data-gaussholder' );
  if ( lqipData ) {
    lqip( img, lqipData.split( ',' ) );
  }
} );
