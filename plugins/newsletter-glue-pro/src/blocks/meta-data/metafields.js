import { AuthorSettings } from './author-settings';
import { DateSettings } from './date-settings';
import { URLSettings } from './url-settings';

export const metafields = {
  1: {
    name: 'author',
    title: 'Author details',
    hasSettings: true,
    control: AuthorSettings,
  },
  2: {
    name: 'issue',
    title: 'Issue number',
  },
  3: {
    name: 'date',
    title: 'Date',
    hasSettings: true,
    control: DateSettings,
  },
  4: {
    name: 'title',
    title: 'Title',
  },
  5: {
    name: 'location',
    title: 'Location',
  },
  6: {
    name: 'readtime',
    title: 'Reading time',
  },
  7: {
    name: 'url',
    title: 'Read online',
    hasSettings: true,
    control: URLSettings,
  },
  8: {
    name: 'meta',
    title: 'Custom meta',
  },
}