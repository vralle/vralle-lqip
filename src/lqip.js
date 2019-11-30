import { canvasRGB } from 'stackblur-canvas';

const lqipHeader = window.GaussholderHeader;

const lqip = function( img, lqipData ) {
  const lqipWidth = parseInt( lqipData[ 1 ], 10 );
  const lqipHeight = parseInt( lqipData[ 2 ], 10 );
  let lqipImg;
  let canvas;
  let ctx;
  let imgLoaded = false;

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
      }
      if ( ! imgLoaded ) {
        img.style.backgroundRepeat = 'no-repeat';
        img.style.backgroundSize = 'cover';
        if ( canvas ) {
          ctx.drawImage( lqipImg, 0, 0, lqipWidth, lqipHeight );
          canvasRGB( canvas, 0, 0, lqipWidth, lqipHeight, 1 );
          img.style.backgroundImage = 'url("' + canvas.toDataURL() + '")';
          img.classList.add( 'guessholder-loaded' );
        }
        // Use modern toBlob? toDataURL has performance issues.
        // if ( canvas.toBlob ) {
        //   canvas.toBlob( function( blob ) {
        //     const url = window.URL.createObjectURL( blob );
        //     // window.URL.revokeObjectURL( url );
        //     img.style.backgroundImage = 'url("' + url + '")';
        //   } );
        // }
      }
      img.classList.remove( 'guessholder-loading' );
    };

    // Use original LQIP sizes and background-size=cover for performance reasons.
    canvas = document.createElement( 'canvas' );
    canvas.width = lqipWidth;
    canvas.height = lqipHeight;
    ctx = canvas.getContext( '2d', { alpha: false } );
    // Ensure smoothing is off
    // ctx.mozImageSmoothingEnabled = false;
    // ctx.webkitImageSmoothingEnabled = false;
    // ctx.msImageSmoothingEnabled = false;
    // ctx.imageSmoothingEnabled = false;

    lqipImg = new Image();
    lqipImg.addEventListener( 'load', onloadLqip );
    lqipImg.addEventListener( 'error', onloadLqip );
    lqipImg.src = 'data:image/jpg;base64,' + reconstituteImage();
  };

  const remove = function() {
    lqipImg = null;
    ctx = null;
    canvas = null;
    img.style.backgroundImage = '';
    img.style.backgroundRepeat = '';
    img.style.backgroundSize = '';
    img.classList.add( 'guessholder-removed' );
    img.classList.remove( 'guessholder-loading' );
    img.classList.remove( 'guessholder-loaded' );
    if ( img.style.length == 0 ) {
      img.removeAttribute( 'style' );
    }
  };

  const lazyloaded = function() {
    img.removeEventListener( 'lazyloaded', lazyloaded );
    imgLoaded = true;
    setTimeout( remove, 800 );
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
