import React from "react";
import {isNotEmpty} from "./utils-helpers";

export function buildComponentFragment({component, props = {}}) {
	if (isNotEmpty(component)) {
		const Component = component;
		return React.cloneElement(component, props)
	}
	return null;
}
