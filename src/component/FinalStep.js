import React,{useContext,Fragment} from 'react';
import Context from '../store/store';
import {Radio,RadioGroup, FormControlLabel, FormControl, FormLabel, TextField } from '@material-ui/core';
import { makeStyles } from '@material-ui/core/styles';
import { __ } from '@wordpress/i18n';
const useStyles = makeStyles({
      margin: {
        marginBottom: '20px',
      },
});
export default function FinalStep(props) {
    const classes = useStyles();
    const ctx = useContext(Context)
    return (
        <Fragment>
            <FormControl component="fieldset" fullWidth className="fieldsetWrapper">
            <FormLabel component="legend" className="mwbFormLabel">{ __('Bingo! You are all set to take advantage of your business. Lastly, we urge you to allow us collect some','woo-refund-and-exchange-lite')} <a href='https://makewebbetter.com/plugin-usage-tracking/' target="_blank" >{__('information','woo-refund-and-exchange-lite') }</a> { __( 'in order to improve this plugin and provide better support. If you want, you can dis-allow anytime settings, We never track down your personal data. Promise!', 'woo-refund-and-exchange-lite') }
                </FormLabel>
                <RadioGroup aria-label="gender" name="consetCheck" value={ctx.formFields['consetCheck']} onChange={ctx.changeHandler} className={classes.margin}>
                    <FormControlLabel value="yes" control={<Radio color="primary"/>} label="Yes" className="mwbFormRadio"/>
                    <FormControlLabel value="no" control={<Radio color="primary"/>} label="No" className="mwbFormRadio"/>
                </RadioGroup>
            </FormControl>
            <FormControl component="fieldset" fullWidth className="fieldsetWrapper">
            <TextField 
                value={ctx.formFields['licenseCode']}
                onChange={ctx.changeHandler} 
                id="licenseCode" 
                name="licenseCode" 
                label={__('Enter your license code')}  variant="outlined" className={classes.margin}/>
            </FormControl>
        </Fragment> 
    );
}