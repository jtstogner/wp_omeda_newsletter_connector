import { __ } from '@wordpress/i18n';

import { AiOutlineBlock } from "react-icons/ai";
import { BiQuestionMark } from "react-icons/bi";
import { HiCodeBracket } from "react-icons/hi2";
import { IoIosSend } from "react-icons/io";

export const tabs = [
  { name: "formatting", label: __('Email formatting', 'newsletter-glue'), icon: HiCodeBracket },
  { name: "sending", label: __('Email sending', 'newsletter-glue'), icon: IoIosSend },
  { name: "features", label: __('Features and Settings', 'newsletter-glue'), icon: AiOutlineBlock },
  { name: "others", label: __('Others', 'newsletter-glue'), icon: BiQuestionMark },
];