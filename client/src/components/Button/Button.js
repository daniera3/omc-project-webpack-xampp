import React from "react";
import {Button as MuiButton, FormControl} from "@material-ui/core";


const Button = ({
                    label,
                    color,
                    disabled,
                    size = "medium",
                    variant = "contained",
                    onClick,
                    type,
                }) => {
    return (
        <FormControl sx={{m: 1, width: '25ch'}} variant="contained">
            <MuiButton
                onClick={onClick}
                color={color}
                disabled={disabled}
                size={size}
                variant={variant}
                type={type}
            >
                {label}
            </MuiButton>
        </FormControl>
    );
};

export default Button;
