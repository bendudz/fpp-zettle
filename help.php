<h1 id="announce-zettle-">Announce Zettle!</h1>
<p><a href="http://makeapullrequest.com"><img src="https://img.shields.io/badge/PRs-welcome-brightgreen.svg?style=flat" alt="Pull Requests Welcome"></a>
    <img src="https://shields.io/badge/fpp-AnnounceZettle-brightgreen" alt="FPP Zettle Logo"></p>
<p>Get yourself an iZettle device, take a donation &amp; trigger an effect on your light show.</p>
<ul>
    <li><a href="#getting-started">Getting Started</a></li>
    <li><a href="#dataplicity-setup">Dataplicity Setup</a></li>
    <li><a href="#fpp">FPP</a></li>
    <li><a href="#pushover-setup">Pushover Setup</a></li>
    <li><a href="#commands">Commands</a></li>
    <li><a href="#notes">Things To Note</a></li>
</ul>
<h2 id="getting-started">Getting Started</h2>
<p>You&#39;ll need an iZettle device to use this plugin.</p>
<p>Either <a href="https://register.zettle.com/gb">register</a> or <a href="https://login.zettle.com/">login</a> with Zettle.</p>
<ul>
    <li>Click &#39;Integrations&#39; (Bottom Left)</li>
    <li>Click &#39;API Keys&#39;
        <img style='height: 100%; width: 100%; object-fit: contain' src="https://fpp-zettle.s3.eu-west-2.amazonaws.com/img/zettle-api-key.png" alt="Zettle API Integrations"></li>
    <li>Click &#39;Create API Key&#39;</li>
    <li>Type a name for your API Key.</li>
    <li>Select &#39;READ:USERINFO and READ:PURCHASE&#39;<br>
        <img style='height: 100%; width: 100%; object-fit: contain' src="https://fpp-zettle.s3.eu-west-2.amazonaws.com/img/zettle-apikeys.png" alt="1"></li>
    <li>Click &#39;Create Key&#39;</li>
</ul>
<p>You will now be presented with 2 attributes you need to copy &amp; keep safe. They won&#39;t be retrievable again so make sure you capture them:</p>
<ul>
    <li>client_id</li>
    <li>API Key
        <img style='height: 100%; width: 100%; object-fit: contain' src="https://fpp-zettle.s3.eu-west-2.amazonaws.com/img/zettle-apikeys-created.png" alt="1"></li>
</ul>
<h2 id="dataplicity-setup">Dataplicity Setup</h2>
<p>This plugin relies on a secure https endpoint so Zettle can send events to you. The easiest way to set up a https endpoint is to use <a href="https://www.dataplicity.com">Dataplicity</a></p>
<p>Greg Macaree has produced an excellent <a href="https://youtu.be/7LeD3dz-uXU">getting started video</a> for Dataplicity. You need to enable the wormhole setting and save the address, you&#39;ll need this later. Watch Greg&#39;s tutorial here:</p>
<iframe width="560" height="315" src="https://www.youtube.com/embed/7LeD3dz-uXU" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
<h2 id="fpp">FPP</h2>
<p>Navigate to your FPP instance. </p>
<p>Click <code>&#39;Content Setup&#39; &gt; &#39;Plugin Manager&#39;</code></p>
<p>Install the <code>Announce Zettle</code> plugin.</p>
<p>Once installed, navigate to <code>&#39;Content Setup&#39; &gt; &#39;Zettle - Setup&#39;</code>.</p>
<p><img style='height: 100%; width: 100%; object-fit: contain' src="https://fpp-zettle.s3.eu-west-2.amazonaws.com/img/setup-init.png" alt="1"></p>
<p>Add your <code>Client ID</code> and <code>Secret</code> to the page &amp; click &#39;Save&#39;</p>
<p><img style='height: 100%; width: 100%; object-fit: contain' src="https://fpp-zettle.s3.eu-west-2.amazonaws.com/img/create-sub.png" alt=""></p>
<p>This will now unlock the ability to add a <code>Subscription</code> that will listen for &#39;purchases&#39; aka donations from our Zettle device.</p>
<p>Enter your Dataplicity wormhole address followed by this plugin&#39;s API event path. ie</p>
<p><code>https://{wormhole address}/api/plugin/fpp-zettle/event</code></p>
<p><code>https://wandering-sheep-0157.dataplicity.io/api/plugin/fpp-zettle/event</code></p>
<p>Add your email address too. This is the address that is notified of any errors sending (or in Zettle&#39;s terminology, &#39;pushing&#39;) a transaction to your Pi.</p>
<p>Save the subscription.</p>
<p><img style='height: 100%; width: 100%; object-fit: contain' src="https://fpp-zettle.s3.eu-west-2.amazonaws.com/img/save-sub.png" alt="1"></p>
<p>The first time you create a subscription you will receive a test notification sent to your Raspberry Pi. This is just the Zettle API notifying you that a subscription has been set up.</p>
<p>Once the subscription has been created successfully, you can then add an effect to be triggered. </p>
<p>Navigate back to the set-up page where you can select the effect to trigger once a transaction is received.</p>
<p><img style='height: 100%; width: 100%; object-fit: contain' src="https://fpp-zettle.s3.eu-west-2.amazonaws.com/img/add-effect-trigger.png" alt="1"></p>
<p>At the moment this plugin can only trigger an effect (ESEQ file), if you have a use case for triggering something else please raise an issue on GitHub, so we can add support.</p>
<p>When a real transaction is received the plugin will log it to a transaction file. You can view transactions in <code>Status / Control &gt; Zettle - Status</code>. This page will also allow you to clear any transactions should you wish. This is mearly for you to see what / who has used your Zettle device to donate at your show.</p>
<p><img style='height: 100%; width: 100%; object-fit: contain' src="https://fpp-zettle.s3.eu-west-2.amazonaws.com/img/status-page.png" alt="1"></p>

<h2 id="pushover-setup">Pushover Setup</h2>
<p>Get notification sent your phone every time a donate is made. Pushover is free to use for 30 days. If you want to use it for longer there is a $5 USD one-time purchase fee. Check out the details at there website: <a href="https://pushover.net/" target="_blank">https://pushover.net</a></p>
<p>To get up and running with Pushover you will need to create an account and get two keys that will be need to everything to work. The two keys you need is the <strong>Application API Token</strong> and <strong>User Key</strong></p>
<p>You can find <strong>User Key</strong> on the first page you go after you login on the rigth hand side</p>
<p>To get the <strong>Application API Token</strong> first you need to create an application.</p>
<p>Navigate to your Pushover dashboard.</p>
<p>Scroll down to <code>Your Applications</code></p>
<p>Click <code>Create an Application/API Token</code></p>
<p>Ender a <code>Name</code> for your application then click <code>Create Application</code></p>
<p>Once your application is created you will see your API Token/Key</p>

<h2 id="commands">Commands</h2>
<p>Zettle Total: Allows you to show what has been raised at the end or during your show using the "Overlay Model Effect Command". The command text can use all text options available.</p>
<p>The command will only work if you are using the "Overlay Model Effect Command" in your zettle setup page.</p>

<h2 id="notes">Things To Note</h2>
<ol>
    <li>For repeat payments to work you will need to iphone running iOS 14. The last phone that supports iOS 14 is an iPhone 6s. You are able to pick up an iphone on facebook market place for as little £30.</li>
    <li>To get repeat payments actived on your zettle account you need to contact them using the live chat or send them an email</li>
    <li>Once you have repeat payments actived on your zettle account you need to turn it on in your zettle app by going to “Settings” > “Payment settings” / “Card”</li>
    <li>The iPhone needs to been connected to the internet and have the zettle app loaded for every thing to work</li>
    <li>Battey life on both the card reader and the phone does not last in the cold so would need to connected to power</li>
    <li>Bluetooth range on the card reader we found is not the best so we recommend that you keep the iPhone with in 5 feet</li>
    <li>The card reader is not water proof and would need to box to keep the water out. Here is a <a href="https://www.amazon.co.uk/dp/B08FC91HHV" target="_blank">link</a> to box that works well</li>
</ol>

<h2 id="privacy-policy">Privacy Policy</h2>
<h3 id="what-we-collect">What We Collect</h3>
<p>Absolutely nothing!</p>
<h3 id="what-we-don-t-collect">What We Don&#39;t Collect</h3>
<p>We do not collect or store any of your personal information. The information you submit via this plugin is transmitted between your Pi &amp; the Zettle API. Any transactions are kept on your device &amp; are retrievable from Zettle using your API Keys (client_id &amp; secret) should you clear them. </p>
