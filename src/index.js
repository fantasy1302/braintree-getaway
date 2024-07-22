import React, { useEffect, useState } from 'react';
import { decodeEntities } from '@wordpress/html-entities';
import braintree from 'braintree-web-drop-in';

const { registerPaymentMethod } = window.wc.wcBlocksRegistry;
const { getSetting } = window.wc.wcSettings;
const settings = getSetting('misha_data', {});
const label = decodeEntities(settings.title);

const Content = () => {
    const [clientToken, setClientToken] = useState(null);
    const [dropinInstance, setDropinInstance] = useState(null);

    useEffect(() => {
        fetch('/wp-json/braintree/v1/getClientToken')
            .then(response => response.json())
            .then(data => {
                console.log('Client token:', data.clientToken);
                setClientToken(data.clientToken);
            })
            .catch(error => {
                console.error('Error fetching client token:', error);
            });
    }, []);

    useEffect(() => {
        if (clientToken) {
            braintree.create({
                authorization: clientToken,
                container: '#bt-dropin',
                paypal: false
            }, (createErr, instance) => {
                if (createErr) {
                    console.error('Create Error:', createErr);
                    return;
                }
                setDropinInstance(instance);
            });
        }
    }, [clientToken]);

    const submitNonce = (event) => {
        event.preventDefault();

        if (dropinInstance) {
            dropinInstance.requestPaymentMethod((err, payload) => {
                if (err) {
                    console.error('Payment method request error:', err);
                    return;
                }

                console.log('Nonce:', payload.nonce);

                fetch('/wp-json/braintree/v1/handleNonce', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',

                    },
                    body: JSON.stringify({
                        nonce: payload.nonce
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        console.log('Server response:', data);
                        if (data.success) {
                            console.log('Payment successful!');
                            setTimeout(() => {
                                document.querySelector('.wc-block-checkout__form').submit();
                            }, 1000)
                            window.location.href = '/checkout/order-received/'
                        } else {
                            alert('Payment failed: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error sending nonce:', error);
                    });
            });
        }
    };

    return (
        <div>
            <div id="bt-dropin"></div>
            <input type="hidden" id="payment_method_nonce" name="payment_method_nonce" />
            <button onClick={submitNonce} type="button">Submit</button>
        </div>
    );
};

const Label = (props) => {
    const { PaymentMethodLabel } = props.components;
    return <PaymentMethodLabel text={label} />;
};

registerPaymentMethod({
    name: 'misha',
    label: <Label />,
    content: <Content />,
    edit: <Content />,
    canMakePayment: () => true,
    ariaLabel: label,
    supports: {
        features: settings.supports,
    },
});
