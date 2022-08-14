<h2 id="getting-started">Getting Started</h2>
<p>You'll need an iZettle device to use this plugin.</p>
<p>Either <a href="https://register.zettle.com/gb">register</a> or <a href="https://login.zettle.com/">login</a> with Zettle.</p>
<ul>
    <li>Click 'Integrations' (Bottom Left)</li>
    <li>Click 'API Keys'</li>
    <li>Click 'Create API Key'</li>
    <li>Type a name for your API Key.</li>
    <li>Select 'READ:USERINFO and READ:PURCHASE'</li>
    <li>Click 'Create Key'</li>
</ul>
<p>You will now be presented with 2 attributes you need to copy &amp; keep safe. They won't be retrievable again so make sure you capture them:</p>
<ul>
    <li>client_id</li>
    <li>API Key</li>
</ul>
<h2 id="fpp">FPP</h2>
<p>Once Announce Zettle is installed, navigate to 'Content Setup' > 'Zettle - Setup'.</p>
<p>Add your Client ID and Secret to the page and click 'Save'</p>

<h2>Effect Setup</h2>
<p>Select a command that you would like to run when a transaction comes in.</p>
<p>If you want just text is display on your matrix you will need to have the following options selected.</p>
<ul>
    <li><strong>Models</strong>: Select the model(s) you want to display the text on</li>
    <li><strong>Auto Enable/Disable</strong>: Enabled</li>
    <li><strong>Effect</strong>: Text</li>
    <li><strong>Color</strong>: What ever color you want the text to be</li>
    <li><strong>Font</strong>: What ever font you want the text to be</li>
    <li><strong>FontSize</strong>: What ever size you want the text to be</li>
    <li><strong>Anti-Aliased</strong>: Checked</li>
    <li><strong>Position</strong>: What ever you want the text to do</li>
    <li><strong>Scroll Speed</strong>: Larger the number this quick the text will move. Note if you have "Center" select in "Position" this feild does not apply.</li>
    <li><strong>Duration</strong>: Larger the number the text will display longer. This only applys to "Center"</li>
    <li>
        <strong>Text</strong>: Thank You {{PAYER_NAME}} for {{AMOUNT}} Note: There is two options available to text override
        <ul>
            <!--li>{{PAYER_NAME}} : Show the name of the person that has donated</li-->
            <li>{{AMOUNT}} : Show the amount the person donated</li>
            <li>{{EVERYTHING}} : Show the amount you have rased from day one</li>
            <li>{{TODAY}} : Show the amount you have rased today</li>
            <li>{{THIS_MONTH}} : Show the amount you have rased this month</li>
        </ul>
    </li>
</ul>

<p>If you just want to test what you have selected just click the "Test" button.</p>
<p>Once you are happy with what you have selected just press the "Save" button and you are done.</p>
