import React from 'react';

/**
 * External dependencies
 */
import classnames from 'classnames';

/**
 * WordPress dependencies
 */
import {
  __EXPERIMENTAL_ELEMENTS as ELEMENTS,
} from '@wordpress/blocks';
import { createHigherOrderComponent, useInstanceId } from '@wordpress/compose';
import { createPortal, useMemo } from '@wordpress/element';
import { addFilter } from '@wordpress/hooks';

/**
 * Override the default block element to include elements styles.
 *
 * @param {Function} BlockListBlock Original component
 * @return {Function}                Wrapped component
 */
const withElementsStyles = createHigherOrderComponent(
  // eslint-disable-next-line react/display-name
  (BlockListBlock) => (props) => {
    const blockElementsContainerIdentifier = `wp-elements-${useInstanceId(
      BlockListBlock
    )}`;

    const styles = useMemo(() => {
      // The .editor-styles-wrapper selector is required on elements styles. As it is
      // added to all other editor styles, not providing it causes reset and global
      // styles to override element styles because of higher specificity.
      const elements = [
        {
          styles: props.attributes.link,
          selector: `.editor-styles-wrapper .ng-block.${blockElementsContainerIdentifier} ${ELEMENTS.link}`
        }
      ];
      const elementCssRules = [];
      for (const { styles: elementStyles, selector } of elements) {
        if (elementStyles) {
          const cssRule = selector + ' { color: ' + elementStyles + '; }';
          elementCssRules.push(cssRule);
        }
      }
      return elementCssRules.length > 0
        ? elementCssRules.join('')
        : undefined;
    }, [
      props.attributes,
      blockElementsContainerIdentifier,
    ]);

    const element = document.body;

    return (
      <>
        {styles &&
          element &&
          createPortal(
            <style
              dangerouslySetInnerHTML={{
                __html: styles,
              }}
            />,
            element
          )}

        <BlockListBlock
          {...props}
          className={
            props.attributes
              ? classnames(
                props.className,
                blockElementsContainerIdentifier
              )
              : props.className
          }
        />
      </>
    );
  }
);

addFilter(
  'editor.BlockListBlock',
  'newsletterglue/editor/css',
  withElementsStyles
);
