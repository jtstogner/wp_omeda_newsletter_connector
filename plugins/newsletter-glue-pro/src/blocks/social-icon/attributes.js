let attrs = {
  service: {
    type: "string",
  },
  url: {
    type: "string",
  },
  url_share: {
    type: "string",
  },
  text: {
    type: "string",
  },
  attrs: {
    type: "object",
  },
  icon_size: {
    type: "string",
    default: "24px",
  },
  gap: {
    type: "string",
    default: "5px",
  },
  mobile_icon_size: {
    type: "string",
    default: "",
  },
  mobile_gap: {
    type: "string",
    default: "",
  },
  align: {
    type: "string",
    default: "none",
  },
  icon_shape: {
    type: "string",
    default: "round",
  },
  icon_color: {
    type: "string",
    default: "black",
  },
  new_window: {
    type: "boolean",
    default: true,
  },
  share_type: {
    type: "string",
    default: "link",
  },
};

export const attributes = attrs;

export default { attributes };
