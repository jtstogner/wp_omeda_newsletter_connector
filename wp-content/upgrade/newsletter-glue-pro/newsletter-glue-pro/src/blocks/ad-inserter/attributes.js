import { theme } from "../../defaults/theme.js";

export const attributes = {
  align: {
    type: "string",
  },
  url: {
    type: "string",
    source: "attribute",
    selector: "img",
    attribute: "src",
    default: nglue_backend.ad_inserter_placeholder_image,
    role: "content",
  },
  alt: {
    type: "string",
    source: "attribute",
    selector: "img",
    attribute: "alt",
    default: "Advertisement Placeholder",
    role: "content",
  },
  title: {
    type: "string",
    source: "attribute",
    selector: "img",
    attribute: "title",
    default: "Advertisement Placeholder",
    role: "content",
  },
  width: {
    type: "number",
    default: 600,
  },
  height: {
    type: "number",
    default: 400,
  },
  adSource: {
    type: "string",
    default: "",
  },
  adImageKey: {
    type: "string",
    default: "image_url",
  },
  adLink: {
    type: "string",
    default: "",
  },
  adTrackingId: {
    type: "string",
    default: "",
  },
  adZoneId: {
    type: "string",
    default: "",
  },
  adZoneName: {
    type: "string",
    default: "",
  },
  clientId: {
    type: "string",
    default: "",
  },
  font: {
    type: "object",
    default: theme.font,
  },
  background: {
    type: "string",
  },
  padding: {
    type: "object",
    default: theme.image.padding,
  },
  border: {
    type: "string",
  },
  borderSize: {
    type: "string",
  },
  radius: {
    type: "string",
  },
  show_in_web: {
    type: "boolean",
    default: true,
  },
  show_in_email: {
    type: "boolean",
    default: true,
  },
  viewport: {
    type: "string",
    default: "Desktop",
  },
};
