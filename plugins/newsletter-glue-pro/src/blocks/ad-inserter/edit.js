import React, { useState, useEffect, useRef } from 'react';

/**
 * External dependencies
 */
import classnames from 'classnames';

/**
 * WordPress dependencies
 */
import {
  BlockAlignmentControl,
  BlockControls,
  InspectorControls,
  useBlockProps,
  __experimentalUseBorderProps as useBorderProps,
} from '@wordpress/block-editor';
import {
  PanelBody,
  TextControl,
  ToggleControl,
  Button,
  SearchControl,
  Spinner,
  Card,
  CardMedia,
  CardHeader,
  CardFooter,
  ResizableBox,
  SelectControl,
  Modal,
  Notice,
} from '@wordpress/components';
import {
  __ } from '@wordpress/i18n';
import { useSelect } from '@wordpress/data';
import { coreStore } from '@wordpress/core-data';
import { blockEditorStore } from '@wordpress/block-editor';
import { image as icon } from '@wordpress/icons';
import { useDebounce } from '@wordpress/compose';
import apiFetch from '@wordpress/api-fetch';

/**
 * Internal dependencies
 */
import './editor.scss';

// Fallback mock data in case the API fails
const mockAds = [
  {
    id: 'ad001',
    title: 'Chorizo Sale Banner up',
    url: 'https://example.com/summer-sale',
    adImage: 'https://blog.lipsumhub.com/wp-content/uploads/2024/09/what-is-a-placeholder-in-advertising-lipsumhub.jpg',
    category: 'promotional',
    placement: 'header',
    group: 'seasonal'
  },
  {
    id: 'ad002',
    title: 'Product Spotlight up',
    url: 'https://example.com/product-spotlight',
    adImage: 'https://adshares.net/uploads/articles/_2023-05/c0e5105265cf5053.png',
    category: 'product',
    placement: 'sidebar',
    group: 'featured'
  },
  {
    id: 'ad003',
    title: 'Newsletter Signup',
    url: 'https://example.com/newsletter-signup',
    adImage: 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTbYEyxYFPsKFY9uIZ9wZfa5EDItoWu_dQ4bg&s',
    category: 'engagement',
    placement: 'footer',
    group: 'conversion'
  },
  {
    id: 'ad004',
    title: 'Holiday Special',
    url: 'https://example.com/holiday-special',
    adImage: 'https://via.placeholder.com/600x400?text=Holiday+Special',
    category: 'promotional',
    placement: 'content',
    group: 'seasonal'
  },
  {
    id: 'ad005',
    title: 'Free Shipping Promo',
    url: 'https://example.com/free-shipping',
    adImage: 'https://via.placeholder.com/468x60?text=Free+Shipping+Promo',
    category: 'promotional',
    placement: 'header',
    group: 'conversion'
  }
];

export default function Edit({ attributes, setAttributes, isSelected, clientId }) {
  const {
    url,
    alt,
    align,
    width,
    height,
    title,
    adSource,
    adImageKey,
    adLink,
    adTrackingId,
    show_in_web,
    show_in_email,
    adZoneId,
    adZoneName,
  } = attributes;

  // Search state
  const [searchTerm, setSearchTerm] = useState('');
  const [isSearching, setIsSearching] = useState(false);
  const [searchResults, setSearchResults] = useState([]);
  const [selectedAd, setSelectedAd] = useState(null);
  const [isApiError, setIsApiError] = useState(false);
  
  // Ad Zone state
  const [adZones, setAdZones] = useState([]);
  const [isLoadingZones, setIsLoadingZones] = useState(false);
  const [selectedZone, setSelectedZone] = useState('');
  const [selectedZoneName, setSelectedZoneName] = useState('');
  const [isZoneModalOpen, setIsZoneModalOpen] = useState(false);
  const [zoneAdsLoaded, setZoneAdsLoaded] = useState(false);
  
  // Track if the image is currently being resized
  const [isResizing, setIsResizing] = useState(false);

  const [isOverlayEnabled, setIsOverlayEnabled] = useState(false);
  
  const { deviceType } = useSelect(select => {
    const { getDeviceType } = select('core/editor') ? select('core/editor') : select('core/edit-site');
    return { deviceType: getDeviceType() }
  }, []);

  const isMobile = deviceType === 'Mobile';
  
  // Default dimensions
  const DEFAULT_WIDTH = 600;
  const DEFAULT_HEIGHT = 400;
  const MIN_SIZE = 50;
  const MAX_SIZE = isMobile ? 304 : 600;

  // Fetch ads from the REST API on component mount
  useEffect(() => {
    fetchAds();
    
    // Store the clientId in block attributes for later use in email rendering
    setAttributes({ clientId });
    
    // Load saved ad zone options for this specific block instance
    loadSavedAdZoneOptions();
  }, []);

  // Function to fetch ads from the REST API
  const fetchAds = async (term = '') => {
    try {
      console.log('Fetching ads from API...');
      const endpoint = term 
        ? `/newsletter-glue/v1/ad-inserter/ads?search=${encodeURIComponent(term)}`
        : '/newsletter-glue/v1/ad-inserter/ads';
      
      const response = await apiFetch({ path: endpoint });
      console.log('API Response:', response);
      
      if (term) {
        setSearchResults(response);
      }
      
      setIsApiError(false);
    } catch (error) {
      console.error('Error fetching ads:', error);
      setIsApiError(true);
      
      // If API fails, show an empty result
      if (term) {
        setSearchResults([]);
      }
    } finally {
      setIsSearching(false);
    }
  };

  // Function to load saved ad zone options for this specific block instance
  const loadSavedAdZoneOptions = async () => {
    try {
      // If no ad zone ID is selected yet, nothing to load
      if (!adZoneId) {
        return;
      }
      
      // Get the post ID
      const postId = wp.data.select('core/editor').getCurrentPostId();
      
      // Fetch saved ad zone options from the REST API
      apiFetch({
        path: `/newsletter-glue/v1/broadstreet/get-ad-zone-options?post_id=${postId}&zone_id=${adZoneId}`,
        method: 'GET',
      }).then((response) => {
        if (response && response.zone_name) {
          // Update the block attributes with the saved zone name
          setAttributes({
            adZoneName: response.zone_name
          });
          
          // Update the state with the saved advertisements
          setZoneAdsLoaded(true);
          console.log('Loaded saved ad zone options:', response);
        }
      }).catch((error) => {
        console.error('Error loading saved ad zone options:', error);
      });
    } catch (error) {
      console.error('Error loading saved ad zone options:', error);
    }
  };

  // Function to fetch ad zones from Broadstreet
  const fetchAdZones = async () => {
    try {
      setIsLoadingZones(true);
      const response = await apiFetch({
        path: '/newsletter-glue/v1/broadstreet/get-ad-zones',
        method: 'POST',
      });
      
      if (response && Array.isArray(response)) {
        // Transform the response into options for the select control
        const zoneOptions = response.map(zone => ({
          label: zone.name,
          value: zone.id.toString(),
          name: zone.name,
        }));
        
        // Add a default option
        zoneOptions.unshift({
          label: __('Select an Ad Zone', 'newsletter-glue'),
          value: '',
          name: '',
        });
        
        setAdZones(zoneOptions);
      } else {
        console.error('Invalid response format for ad zones:', response);
        setAdZones([]);
      }
    } catch (error) {
      console.error('Error fetching ad zones:', error);
      setAdZones([]);
    } finally {
      setIsLoadingZones(false);
    }
  };

  // Handle ad zone selection
  const handleZoneSelection = async (zoneId) => {
    if (!zoneId) return;
    
    try {
      setIsSearching(true);
      
      // Find the zone name from the selected zone ID
      const selectedZoneObj = adZones.find(zone => zone.value === zoneId);
      const zoneName = selectedZoneObj ? selectedZoneObj.name : '';
      
      // Store the zone ID and name in the block attributes
      setAttributes({
        adZoneId: zoneId,
        adZoneName: zoneName,
      });
      
      setSelectedZoneName(zoneName);
      setZoneAdsLoaded(true);
      
      // Get the current post ID
      const postId = wp.data.select('core/editor').getCurrentPostId();
      
      // Save the zone ID to our custom endpoint with post ID and zone ID
      await apiFetch({
        path: '/newsletter-glue/v1/broadstreet/save-ad-zone-options',
        method: 'POST',
        data: { 
          zone_id: zoneId,
          zone_name: zoneName,
          post_id: postId
        }
      });
      
      // Fetch the ads from the zone but don't display them in search results
      const response = await apiFetch({
        path: '/newsletter-glue/v1/broadstreet/get-ad-zone-advertisements',
        method: 'POST',
        data: { zone_id: zoneId },
      });
      
      if (response && Array.isArray(response)) {
        // Store the ad IDs in our custom endpoint with post ID and zone ID
        await apiFetch({
          path: '/newsletter-glue/v1/broadstreet/save-ad-zone-options',
          method: 'POST',
          data: { 
            advertisements: JSON.stringify(response),
            post_id: postId,
            zone_id: zoneId
          }
        });
      }
    } catch (error) {
      console.error('Error handling zone selection:', error);
    } finally {
      setIsSearching(false);
      setIsZoneModalOpen(false);
    }
  };
  
  // Open the ad zone modal
  const openZoneModal = () => {
    fetchAdZones();
    setIsZoneModalOpen(true);
  };
  
  // Close the ad zone modal
  const closeZoneModal = () => {
    setIsZoneModalOpen(false);
  };
  
  // Clear the selected ad zone
  const clearSelectedZone = () => {
    setAttributes({
      adZoneId: '',
      adZoneName: '',
    });
    setSelectedZone('');
    setSelectedZoneName('');
    setZoneAdsLoaded(false);
  };

  const borderProps = useBorderProps(attributes);

  const classes = classnames({
    'ng-block': true,
    [`align${align}`]: align,
    'is-resized': width || height,
    'has-custom-border': !!borderProps.className,
    'is-resizing': isResizing,
    'is-selected': isSelected,
  });

  const blockProps = useBlockProps({
    className: classes,
  });

  // Get placeholder URL based on whether an ad zone is selected
  const getPlaceholderUrl = () => {
    if (zoneAdsLoaded && adZoneId) {
      return 'https://via.placeholder.com/600x400?text=Ad+Zone:+' + encodeURIComponent(adZoneName || adZoneId);
    }
    return 'https://via.placeholder.com/600x400?text=Select+an+Ad';
  };
  
  const placeholderUrl = url || getPlaceholderUrl();
  
  // Determine if we have a real ad selected or just the placeholder
  const hasRealAd = adSource || selectedAd;
  
  const getCurrentWidth = () => {
    return typeof width === 'number' && width > 0 ? width : DEFAULT_WIDTH;
  };
  
  const getCurrentHeight = () => {
    return typeof height === 'number' && height > 0 ? height : DEFAULT_HEIGHT;
  };

  // Initialize selected zone from attributes if available
  useEffect(() => {
    if (adZoneId) {
      setSelectedZone(adZoneId);
      setSelectedZoneName(adZoneName || '');
      setZoneAdsLoaded(true);
    }
  }, []);

  // Load saved ad zone options when component mounts
  useEffect(() => {
    // If we already have adZoneId and adZoneName in attributes, use those
    if (adZoneId && adZoneName) {
      setZoneAdsLoaded(true);
      return;
    }
    
    // Otherwise, try to load from saved options
    const postId = wp.data.select('core/editor').getCurrentPostId();
    
    if (postId) {
      apiFetch({
        path: `/newsletter-glue/v1/broadstreet/get-ad-zone-options?post_id=${postId}&zone_id=${adZoneId}`,
        method: 'GET'
      })
      .then(response => {
        console.log('Loaded ad zone options:', response);
        
        if (response.zone_id && response.zone_name) {
          // Update block attributes
          setAttributes({
            adZoneId: response.zone_id,
            adZoneName: response.zone_name
          });
          
          // Update state
          setZoneAdsLoaded(true);
        }
      })
      .catch(error => {
        console.error('Error loading ad zone options:', error);
      });
    }
  }, []);

  // Simple search function without debounce for more direct feedback
  const handleSearchChange = (term) => {
    setSearchTerm(term);
    
    if (!term) {
      setSearchResults([]);
      return;
    }
    
    setIsSearching(true);
    
    // Log the search term
    console.log('Searching for:', term);
    
    // Fetch ads from the API with the search term
    fetchAds(term);
  };

  // Handle ad selection
  const handleSelectAd = (ad) => {
    console.log('Selected ad:', ad);
    setSelectedAd(ad);
    
    // Update block attributes with selected ad data
    setAttributes({
      url: ad.adImage,
      adLink: ad.url,
      alt: ad.title,
      title: ad.title,
      // You can also store the ad ID or other metadata
      adSource: ad.id,
      adTrackingId: `ad-${ad.id}`,
    });

    // Clear search results
    setSearchResults([]);
    setSearchTerm('');
  };

  // Handle image resize
  const handleImageResize = (event, direction, elt) => {
    // Get the actual size from the element
    const newWidth = Math.round(elt.clientWidth);
    const newHeight = Math.round(elt.clientHeight);
    
    // Apply reasonable limits
    const safeWidth = Math.min(Math.max(newWidth, MIN_SIZE), MAX_SIZE);
    const safeHeight = Math.min(Math.max(newHeight, MIN_SIZE), MAX_SIZE);
    
    // Update attributes directly (no intermediate state)
    setAttributes({
      width: safeWidth,
      height: safeHeight,
    });
  };

  // Handle resize start
  const handleResizeStart = () => {
    setIsResizing(true);
  };

  // Handle resize stop
  const handleResizeStop = () => {
    setIsResizing(false);
  };

  // Handle input field changes for width/height
  const updateDimension = (dimension, value) => {
    const numValue = parseInt(value, 10);
    
    if (isNaN(numValue)) return;
    
    const safeValue = Math.max(MIN_SIZE, Math.min(numValue, MAX_SIZE));
    
    if (dimension === 'width') {
      setAttributes({ width: safeValue });
    } else if (dimension === 'height') {
      setAttributes({ height: safeValue });
    }
  };

  return (
    <>
      <BlockControls group="block">
        <BlockAlignmentControl
          value={align}
          onChange={(nextAlign) => setAttributes({ align: nextAlign })}
        />
      </BlockControls>
      <InspectorControls>
        {nglue_backend.license_tier === 'new_pro' && (
        <PanelBody title={__('Ad Selection', 'newsletter-glue')} initialOpen={true}>
          {!zoneAdsLoaded ? (
            <>
              <SearchControl
                value={searchTerm}
                onChange={handleSearchChange}
                placeholder={__('Search for ads...', 'newsletter-glue')}
                className="ad-search-control"
              />
              
              {nglue_backend.license_tier === 'new_pro' && nglue_backend.broadstreet_has_connection && (
              <div style={{ marginTop: '10px', marginBottom: '10px' }}>
                <Button 
                  variant="secondary" 
                  onClick={openZoneModal}
                  style={{ width: '100%' }}
                  isBusy={isLoadingZones}
                >
                  {__('Insert from Ad Zone', 'newsletter-glue')}
                </Button>
              </div>
              )}
              
              {isSearching && (
                <div className="ad-search-loading">
                  <Spinner />
                  <p>{__('Searching ads...', 'newsletter-glue')}</p>
                </div>
              )}
              
              {searchResults.length > 0 && (
                <div className="ad-search-results" style={{ padding: '1%', margin: '1%' }}>
                  {searchResults.map(ad => (
                    <Card key={ad.id} className="ad-search-result-item">
                      <CardHeader>
                        <strong>{ad.title}</strong>
                      </CardHeader>
                      <CardMedia>
                        <img 
                          src={ad.adImage} 
                          alt={ad.title}
                          style={{ maxHeight: '100px', width: 'auto', margin: '0 auto' }}
                        />
                      </CardMedia>
                      <CardFooter>
                        <Button 
                          variant="primary" 
                          onClick={() => handleSelectAd(ad)}
                          isSmall
                        >
                          {__('Select', 'newsletter-glue')}
                        </Button>
                        <div className="ad-meta">
                          <span className="ad-category">{ad.category}</span>
                          <span className="ad-placement">{ad.placement}</span>
                        </div>
                      </CardFooter>
                    </Card>
                  ))}
                </div>
              )}
              
              {searchTerm && searchResults.length === 0 && !isSearching && (
                <p className="no-results">{__('No ads found matching your search.', 'newsletter-glue')}</p>
              )}
            </>
          ) : (
            <div className="ad-zone-selected">
              <Notice status="success" isDismissible={false}>
                <p>
                  {__('Ads will be automatically loaded from:', 'newsletter-glue')} 
                  <strong>{adZoneName || adZoneId}</strong>
                </p>
              </Notice>
              <div style={{ marginTop: '10px' }}>
                <Button 
                  variant="secondary" 
                  onClick={clearSelectedZone}
                  isSmall
                >
                  {__('Change Ad Zone', 'newsletter-glue')}
                </Button>
              </div>
            </div>
          )}
        </PanelBody>
        )}
        {nglue_backend.license_tier === 'new_pro' && (
        <PanelBody title={__('Ad Settings', 'newsletter-glue')}>
          <TextControl
            label={__('Ad Source', 'newsletter-glue')}
            help={__('Enter the ad source URL or identifier', 'newsletter-glue')}
            value={adSource}
            onChange={(value) => setAttributes({ adSource: value })}
          />
          <TextControl
            label={__('Ad Image Key', 'newsletter-glue')}
            help={__('Enter the key that contains the ad image URL', 'newsletter-glue')}
            value={adImageKey}
            onChange={(value) => setAttributes({ adImageKey: value })}
          />
          <TextControl
            label={__('Ad Link', 'newsletter-glue')}
            help={__('URL where the ad should link to', 'newsletter-glue')}
            value={adLink}
            onChange={(value) => setAttributes({ adLink: value })}
          />
          <TextControl
            label={__('Tracking ID', 'newsletter-glue')}
            help={__('Optional tracking ID for analytics', 'newsletter-glue')}
            value={adTrackingId}
            onChange={(value) => setAttributes({ adTrackingId: value })}
          />
          <TextControl
            label={__('Alt Text', 'newsletter-glue')}
            help={__('Alternative text for the image', 'newsletter-glue')}
            value={alt}
            onChange={(value) => setAttributes({ alt: value })}
          />
          <TextControl
            label={__('Title', 'newsletter-glue')}
            help={__('Title attribute for the image', 'newsletter-glue')}
            value={title}
            onChange={(value) => setAttributes({ title: value })}
          />
          <div style={{ display: 'flex', gap: '10px', marginBottom: '10px' }}>
            <TextControl
              type="number"
              label={__('Width', 'newsletter-glue')}
              value={width || ''}
              onChange={(value) => updateDimension('width', value)}
              min={MIN_SIZE}
              max={MAX_SIZE}
            />
            <TextControl
              type="number"
              label={__('Height', 'newsletter-glue')}
              value={height || ''}
              onChange={(value) => updateDimension('height', value)}
              min={MIN_SIZE}
              max={MAX_SIZE}
            />
          </div>
        </PanelBody>
        )}
        <PanelBody title={__('Display Settings', 'newsletter-glue')}>
          <ToggleControl
            label={__('Show in Web', 'newsletter-glue')}
            checked={show_in_web}
            onChange={() => setAttributes({ show_in_web: !show_in_web })}
          />
          <ToggleControl
            label={__('Show in Email', 'newsletter-glue')}
            checked={show_in_email}
            onChange={() => setAttributes({ show_in_email: !show_in_email })}
          />
        </PanelBody>
      </InspectorControls>

      {isZoneModalOpen && (
        <Modal
          title={__('Select Ad Zone', 'newsletter-glue')}
          onRequestClose={closeZoneModal}
          className="ad-zone-modal"
        >
          {isLoadingZones ? (
            <div style={{ display: 'flex', justifyContent: 'center', padding: '20px' }}>
              <Spinner />
              <p style={{ marginLeft: '10px' }}>{__('Loading ad zones...', 'newsletter-glue')}</p>
            </div>
          ) : (
            <>
              {adZones.length > 0 ? (
                <div style={{ padding: '20px' }}>
                  <SelectControl
                    label={__('Available Ad Zones', 'newsletter-glue')}
                    value={selectedZone}
                    options={adZones}
                    onChange={(value) => setSelectedZone(value)}
                  />
                  <div style={{ marginTop: '20px', display: 'flex', justifyContent: 'flex-end' }}>
                    <Button
                      variant="secondary"
                      onClick={closeZoneModal}
                      style={{ marginRight: '10px' }}
                    >
                      {__('Cancel', 'newsletter-glue')}
                    </Button>
                    <Button
                      variant="primary"
                      onClick={() => handleZoneSelection(selectedZone)}
                      disabled={!selectedZone}
                      isBusy={isSearching}
                    >
                      {__('Select Ad Zone', 'newsletter-glue')}
                    </Button>
                  </div>
                </div>
              ) : (
                <div style={{ padding: '20px' }}>
                  <p>{__('No ad zones found. Please make sure you have ad zones set up in Broadstreet.', 'newsletter-glue')}</p>
                  <div style={{ marginTop: '20px', display: 'flex', justifyContent: 'flex-end' }}>
                    <Button
                      variant="secondary"
                      onClick={closeZoneModal}
                    >
                      {__('Close', 'newsletter-glue')}
                    </Button>
                  </div>
                </div>
              )}
            </>
          )}
        </Modal>
      )}

      <div {...blockProps}>

        {nglue_backend.license_tier === 'new_pro' && ((typeof nglue_backend.broadstreet_access_token === 'string' && nglue_backend.broadstreet_access_token.trim() !== '' && nglue_backend.broadstreet_available) || nglue_backend.advanced_ads_available) && (

<figure className="wp-block-image" style={{ display: 'flex', justifyContent: 'center' }}>
{hasRealAd ? (
  <ResizableBox
    size={{
      width: getCurrentWidth(),
      height: getCurrentHeight(),
    }}
    minWidth={MIN_SIZE}
    maxWidth={MAX_SIZE}
    minHeight={MIN_SIZE}
    maxHeight={MAX_SIZE}
    lockAspectRatio={false}
    enable={{
      top: true,
      right: true,
      bottom: true,
      left: true,
      topRight: true,
      bottomRight: true,
      bottomLeft: true,
      topLeft: true,
    }}
    onResizeStart={() => setIsResizing(true)}
    onResizeStop={(event, direction, elt, delta) => {
      setIsResizing(false);
      setAttributes({
        width: parseInt(getCurrentWidth() + delta.width, 10),
        height: parseInt(getCurrentHeight() + delta.height, 10),
      });
    }}
  >
    <img
      src={url}
      alt={alt}
      title={title}
      style={{
        width: '100%',
        height: '100%',
        ...borderProps.style,
      }}
      {...borderProps.attributes}
    />
  </ResizableBox>
) : (
  <div className="ad-inserter-placeholder" style={{ position: 'relative', width: '100%' }}>
    <img
      src={placeholderUrl}
      alt={alt || __('Advertisement Placeholder', 'newsletter-glue')}
      title={title || __('Advertisement Placeholder', 'newsletter-glue')}
      style={{
        width: '100%',
        height: 'auto',
        ...borderProps.style,
      }}
      {...borderProps.attributes}
    />
    
    {/* Ad Zone Indicator - Show when an ad zone is selected */}
    {zoneAdsLoaded && adZoneId && (
      <div className="ad-zone-indicator" style={{
        position: 'absolute',
        top: 0,
        left: 0,
        right: 0,
        backgroundColor: 'rgba(0, 120, 200, 0.8)',
        color: 'white',
        padding: '8px 12px',
        fontSize: '14px',
        fontWeight: 'bold',
        textAlign: 'center',
        borderTopLeftRadius: borderProps.style.borderRadius || '0',
        borderTopRightRadius: borderProps.style.borderRadius || '0',
      }}>
        <span className="dashicons dashicons-update" style={{ marginRight: '5px' }}></span>
        {__('Dynamic Ad Zone:', 'newsletter-glue')} {adZoneName || adZoneId}
      </div>
    )}
    
    {/* Initial placeholder overlay - Show only when no ad and no ad zone is selected */}
    {!zoneAdsLoaded && !hasRealAd && (
      <div className="ad-inserter-overlay" style={{
        position: 'absolute',
        top: 0,
        left: 0,
        right: 0,
        bottom: 0,
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        backgroundColor: '#e5e5e5',
        color: '#333',
      }}>
        <div className="ad-inserter-info" style={{ textAlign: 'center', padding: '20px' }}>
          <p>{__('Search for an ad manually or select an ad zone.', 'newsletter-glue')}</p>
        </div>
      </div>
    )}
  </div>
)}
</figure>

        )} 

        {(nglue_backend.broadstreet_access_token === '' ) && nglue_backend.license_tier === 'new_pro' && nglue_backend.broadstreet_available && ! nglue_backend.advanced_ads_available && (
        <div
          style={{
            display: 'block',
            width: '600px',
            height: '400px',
            backgroundColor: '#e5e5e5',
          }}
        >
          <div style={{
            display: 'flex',
            justifyContent: 'center',
            alignItems: 'center',
            height: '100%',
            backgroundColor: '#e5e5e5',
          }}>
            <div style={{
              textAlign: 'center',
              backgroundColor: '#fff',
              padding: '5px 20px',
              borderRadius: '5px',
            }}>
              <p style={{ fontSize: '16px', color: '#333', margin: '5px' }}>
                {__('Please connect to Broadstreet to use this block', 'newsletter-glue')}
              </p>
              <a
                href="/wp-admin/admin.php?page=newsletter-glue-settings#/broadstreet"
                target="_blank"
                rel="noopener noreferrer"
                style={{
                  textDecoration: 'none',
                  color: '#0088a0',
                }}
              >
                {__('Click here to connect', 'newsletter-glue')}
              </a>
            </div>
          </div>
        </div>
        )}
        
        {nglue_backend.license_tier !== 'new_pro' && (
        <div
          style={{
            display: 'block',
            width: '600px',
            height: '400px',
            backgroundColor: '#e5e5e5',
          }}
        >
          <div style={{
            display: 'flex',
            justifyContent: 'center',
            alignItems: 'center',
            height: '100%',
            backgroundColor: '#e5e5e5',
          }}>
            <div style={{
              textAlign: 'center',
              backgroundColor: '#fff',
              padding: '5px 20px',
              borderRadius: '5px',
            }}>
              <p style={{ fontSize: '16px', color: '#333', margin: '5px' }}>
                {__('The Ad Inserter block is available in the Pro plan.', 'newsletter-glue')}
              </p>
              <a
                href="https://newsletterglue.com/pricing/?utm_source=wp_admin&utm_medium=block&utm_campaign=ad_inserter&utm_content=upgrade"
                target="_blank"
                rel="noopener noreferrer"
                style={{
                  textDecoration: 'none',
                  color: '#0088a0',
                }}
              >
                {__('Click here to upgrade', 'newsletter-glue')}
              </a>
            </div>
          </div>
        </div>
        )}
      </div>
    </>
  );
}
