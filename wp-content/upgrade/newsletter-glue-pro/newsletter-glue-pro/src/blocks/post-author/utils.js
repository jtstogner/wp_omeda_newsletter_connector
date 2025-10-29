export function getSocialColor(social, defaultColor) {

  if (social === 'twitter') {
    return '#1DA1F2';
  }

  if (social === 'instagram') {
    return '#ed4956';
  }

  if (social === 'facebook') {
    return '#1877F2';
  }

  if (social === 'youtube') {
    return '#FF0000';
  }

  if (social === 'linkedin') {
    return '#0e76a8';
  }

  if (social === 'twitch') {
    return '#9047FF';
  }

  if (social === 'tiktok') {
    return '#fe2c55';
  }

  return defaultColor;
}