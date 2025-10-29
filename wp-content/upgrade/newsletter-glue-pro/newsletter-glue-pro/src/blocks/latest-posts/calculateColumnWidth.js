export function calculateColumnWidth(attributes, setAttributes) {

  let DivOne;
  let DivTwo;
  let imageDivFlex = '100%';
  let dataDivFlex = '100%';
  let split;
  let itemFlexBase;

  const { padding, margin, table_ratio, image_position, columns_num } = attributes;

  var WrapperWidth = 600 - (parseInt(padding.left) + parseInt(padding.right) + parseInt(margin.left) + parseInt(margin.right));

  if (table_ratio == '70_30') {
    split = table_ratio.split('_');
    DivOne = split[0];
    DivTwo = split[1];
  }

  if (table_ratio == '30_70') {
    split = table_ratio.split('_');
    DivOne = split[0];
    DivTwo = split[1];
  }

  if (table_ratio == '50_50') {
    split = table_ratio.split('_');
    DivOne = split[0];
    DivTwo = split[1];
  }

  if (image_position === 'right') {
    if (table_ratio == '70_30') {
      split = table_ratio.split('_');
      DivTwo = split[0];
      DivOne = split[1];
    }

    if (table_ratio == '30_70') {
      split = table_ratio.split('_');
      DivTwo = split[0];
      DivOne = split[1];
    }

    if (table_ratio == '50_50') {
      split = table_ratio.split('_');
      DivTwo = split[0];
      DivOne = split[1];
    }
  }

  if (DivOne && DivTwo) {
    imageDivFlex = Math.floor(((DivOne / 100) * WrapperWidth) - 10) + 'px';
    dataDivFlex = Math.floor(((DivTwo / 100) * WrapperWidth) - 10) + 'px';
  }

  if (columns_num == 'two') {
    itemFlexBase = Math.floor((WrapperWidth / 2) - 10) + 'px';
  }

  setAttributes({ div1: imageDivFlex, div2: dataDivFlex, containerWidth: WrapperWidth + 'px', itemBase: itemFlexBase })

}