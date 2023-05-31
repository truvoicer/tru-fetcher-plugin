import React from 'react';
import {isSet} from "../../../../library/helpers/utils-helpers";

function LabelBuilder({errors, field, showError = true}) {
	const showLabel = () => {
		if (!isSet(field?.showLabel)) {
			return true;
		}
		if (field.showLabel && field.label) {
			return true;
		}
		return false;
	}
	return (
		<>
			{showLabel() &&
				<>
					{field.label}
					<label className="text-black" htmlFor={field.name}>
						{showError &&
							<span className={"tr-news-app--red site-form--error--field ml-3"}>
								{errors[field.name]}
							</span>
						}
					</label>
				</>
			}
		</>
	);
}

export default LabelBuilder;
