import { theme } from "../../defaults/theme.js";

let attrs = {
  placeholder: {
    type: "string",
  },
  content: {
    type: "string",
    source: "html",
    selector: "li",
    default: "",
    role: "content",
  },
  spacing: {
    type: "string",
    default: theme.list.spacing,
  },
  mobile_list_spacing: {
    type: "string",
    default: theme.mobile.list.spacing,
  },
};

export const attributes = attrs;

export default { attributes };
