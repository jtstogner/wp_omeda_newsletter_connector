import { CheckboxSettings } from './checkbox-settings';
import { NameSettings } from './name-settings';

export const metafields = {
  1: {
    name: 'heading',
    title: 'Form heading',
  },
  2: {
    name: 'description',
    title: 'Form description',
  },
  3: {
    name: 'name',
    title: 'Name',
    hasSettings: true,
    control: NameSettings,
  },
  4: {
    name: 'checkbox',
    title: 'Checkbox',
    hasSettings: true,
    control: CheckboxSettings,
  },
}