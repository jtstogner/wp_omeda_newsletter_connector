import React from "react";

import {
  __experimentalBoxControl as BoxControl,
  CustomSelectControl,
  __experimentalNumberControl as NumberControl,
  RangeControl,
  SelectControl,
  __experimentalToolsPanelItem as ToolsPanelItem,
  __experimentalUnitControl as UnitControl,
} from "@wordpress/components";

import styled from "@emotion/styled";

import { units } from "../defaults/units.js";
import { extrafontweights } from "../defaults/weights.js";

const SingleColumnItem = styled(ToolsPanelItem)`
  grid-column: span 1;
`;

export const SettingsPane = (props) => {
  const { settings, attributes, setAttributes } = props;

  return (
    <>
      {settings.map(function (item, i) {
        var ComponentName = item.is_single ? SingleColumnItem : ToolsPanelItem;
        var fallbackValue = item.default ? item.default : undefined;

        if (item.type === "customselect") {
          if (item.value === "fontweight") {
            if (attributes.level) {
              if (attributes[`h${attributes.level}_font`].key) {
                if (
                  ["inter"].includes(
                    attributes[`h${attributes.level}_font`].key
                  )
                ) {
                  item.options = extrafontweights;
                }
              }
            } else {
              if (attributes.font && attributes.font.key) {
                if (["inter"].includes(attributes.font.key)) {
                  item.options = extrafontweights;
                }
              }
            }
          }
        }

        const onlyHorizontal =
          item.noHorizontal && item.noHorizontal ? true : false;

        return (
          <ComponentName
            hasValue={() => attributes[item.value] != fallbackValue}
            label={item.label}
            //onDeselect={() => setAttributes({ [item.value]: fallbackValue })}
            isShownByDefault
            key={`settingItem-${i}`}
          >
            {item.type === "section" && <SectionTitle label={item.label} />}
            {item.type === "select" && (
              <SelectControl
                label={item.label}
                value={attributes[item.value]}
                options={item.options}
                onChange={(newValue) =>
                  setAttributes({ [item.value]: newValue })
                }
              />
            )}
            {item.type === "customselect" && (
              <CustomSelectControl
                label={item.label}
                options={item.options}
                onChange={({ selectedItem }) =>
                  setAttributes({ [item.value]: selectedItem })
                }
                value={item.options.find(
                  (option) => option.key === attributes[item.value].key
                )}
              />
            )}
            {item.type === "unit" && (
              <UnitControl
                label={item.label}
                value={attributes[item.value]}
                onChange={(newValue) => {
                  setAttributes({ [item.value]: newValue });
                }}
                units={units}
              />
            )}
            {item.type === "number" && (
              <NumberControl
                label={item.label}
                onChange={(newValue) =>
                  setAttributes({ [item.value]: parseFloat(newValue) })
                }
                step={item.step ? item.step : "any"}
                value={attributes[item.value]}
                spinControls="custom"
                min={0}
              />
            )}
            {item.type === "boxcontrol" && (
              <BoxControl
                label={item.label}
                values={attributes[item.value]}
                onChange={(newValue) =>
                  setAttributes({ [item.value]: newValue })
                }
                units={units}
                allowReset={false}
                sides={
                  onlyHorizontal
                    ? ["top", "bottom"]
                    : ["top", "right", "bottom", "left"]
                }
              />
            )}
            {item.type == "range" && (
              <RangeControl
                onChange={(newValue) =>
                  setAttributes({ [item.value]: newValue })
                }
                value={attributes[item.value]}
                min={10}
                max={100}
                allowReset={false}
              />
            )}
          </ComponentName>
        );
      })}
    </>
  );
};

function SectionTitle(props) {
  const styles = {
    fontWeight: 500,
    fontSize: "13px",
    margin: 0,
  };
  return <p style={styles}>{props.label}</p>;
}
