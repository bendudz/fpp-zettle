<h2 id="getting-started">Create Subscription</h2>
<p>A payement subscription is need to for zettle to talk to FPP. For this to work your FPP need to be accessible to the internet.</p>
<p>This can be done with a service called Dataplicity. Free to use for one device.</p>
<p>Either <a href="https://www.dataplicity.com/" target="_blank">register</a> or <a href="https://www.dataplicity.com/app" target="_blank">login</a> with Dataplicity.</p>
<ul>
    <li>Add Device</li>
    <li>Options:
        <ul>
            <li>
                Option One: install in brower:
                <ul>
                    <li>In <strong>Create Subscription</strong> page copy "Dataplicity Install Command" in to the text box and press in the "Install" button</li>
                </ul>
            </li>
            <li>
                Option Two: SSL Shell
                <ul>
                    <li>In FPP to Help > SSL Shell and login with <b>Username</b>: fpp <b>Password</b>: falcon</li>
                    <li>In the sell copy and paste the command and press enter</li>
                </ul>
            </li>
        </ul>
    </li>
    <li>Once the script has been installed go back to Dataplicity Click on 'Your devices' you will see FPP listed</li>
    <li>Click on the device in the right menu click the secord option and enable 'Wormhole'</li>
    <li>Once this is done you will see a url. This is what zettle will use to talk to your FPP</li>
</ul>

<p>With the url from Dataplicity copy it in to the "Destination" text box and supply a contact email.</p>
<p>Contact Email is used if there is an error sending payment details back to FPP.</p>
