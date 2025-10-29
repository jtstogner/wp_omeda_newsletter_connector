import { theme } from '../../defaults/theme.js';

let attrs = {
  hasBorder: {
    type: 'boolean',
    default: true,
  },
  "hasFixedLayout": {
    "type": "boolean",
    "default": true
  },
  "caption": {
    "type": "string",
    "source": "html",
    "selector": "figcaption",
    "default": ""
  },
  "head": {
    "type": "array",
    "default": [],
    "source": "query",
    "selector": "thead tr",
    "query": {
      "cells": {
        "type": "array",
        "default": [],
        "source": "query",
        "selector": "td,th",
        "query": {
          "content": {
            "type": "string",
            "source": "html"
          },
          "tag": {
            "type": "string",
            "default": "td",
            "source": "tag"
          },
          "scope": {
            "type": "string",
            "source": "attribute",
            "attribute": "scope"
          },
          "align": {
            "type": "string",
            "source": "attribute",
            "attribute": "data-align"
          },
          "colspan": {
            "type": "string",
            "source": "attribute",
            "attribute": "colspan"
          },
          "rowspan": {
            "type": "string",
            "source": "attribute",
            "attribute": "rowspan"
          }
        }
      }
    }
  },
  "body": {
    "type": "array",
    "default": [],
    "source": "query",
    "selector": "tbody tr",
    "query": {
      "cells": {
        "type": "array",
        "default": [],
        "source": "query",
        "selector": "td,th",
        "query": {
          "content": {
            "type": "string",
            "source": "html"
          },
          "tag": {
            "type": "string",
            "default": "td",
            "source": "tag"
          },
          "scope": {
            "type": "string",
            "source": "attribute",
            "attribute": "scope"
          },
          "align": {
            "type": "string",
            "source": "attribute",
            "attribute": "data-align"
          },
          "colspan": {
            "type": "string",
            "source": "attribute",
            "attribute": "colspan"
          },
          "rowspan": {
            "type": "string",
            "source": "attribute",
            "attribute": "rowspan"
          }
        }
      }
    }
  },
  "foot": {
    "type": "array",
    "default": [],
    "source": "query",
    "selector": "tfoot tr",
    "query": {
      "cells": {
        "type": "array",
        "default": [],
        "source": "query",
        "selector": "td,th",
        "query": {
          "content": {
            "type": "string",
            "source": "html"
          },
          "tag": {
            "type": "string",
            "default": "td",
            "source": "tag"
          },
          "scope": {
            "type": "string",
            "source": "attribute",
            "attribute": "scope"
          },
          "align": {
            "type": "string",
            "source": "attribute",
            "attribute": "data-align"
          },
          "colspan": {
            "type": "string",
            "source": "attribute",
            "attribute": "colspan"
          },
          "rowspan": {
            "type": "string",
            "source": "attribute",
            "attribute": "rowspan"
          }
        }
      }
    }
  },
  font: {
    type: 'object',
    default: theme.font,
  },
  fontsize: {
    type: 'string',
    default: theme.table.fontsize,
  },
  lineheight: {
    type: 'number',
    default: theme.table.lineheight,
  },
  background: {
    type: 'string',
  },
  background2: {
    type: 'string',
  },
  backgroundhead: {
    type: 'string',
  },
  backgroundfoot: {
    type: 'string',
  },
  color: {
    type: 'string',
  },
  link: {
    type: 'string',
    default: theme.colors.primary,
  },
  border: {
    type: 'string',
  },
  margin: {
    type: 'object',
    default: theme.table.margin,
  },
  padding: {
    type: 'object',
    default: theme.table.padding,
  },
  style: {
    type: 'string',
    default: 'default',
  },
  show_in_web: {
    type: 'boolean',
    default: true,
  },
  show_in_email: {
    type: 'boolean',
    default: true,
  },
  viewport: {
    type: 'string',
    default: 'Desktop',
  },
  inherited: {
    type: 'boolean',
    default: false,
  },
};

if (theme.mobile.table) {
  attrs[`mobile_size`] = {
    type: 'string',
    default: theme.mobile.table.fontsize
  }
  attrs[`mobile_lineheight`] = {
    type: 'number',
    default: theme.mobile.table.lineheight
  }
  attrs[`mobile_margin`] = {
    type: 'object',
    default: theme.mobile.table.margin
  }
  attrs[`mobile_padding`] = {
    type: 'object',
    default: theme.mobile.table.padding
  }
}

export const attributes = attrs;

export default { attributes }