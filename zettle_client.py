from pathlib import Path

import requests
import time
from requests import request

import json
from requests import RequestException
from requests.auth import AuthBase
from os.path import exists
from uuid import uuid1


oauth_base = "https://oauth.zettle.com"
subscriptions_url = "https://pusher.izettle.com/organizations/self/subscriptions"


class TokenAuth(AuthBase):
    """Refreshes Zettle token, for use with all Zettle requests"""

    def __init__(self, client_id: str, client_secret: str):
        self.client_id = client_id
        self.client_secret = client_secret

        # Initialise with no token and instant expiry
        self.token = None
        self.token_expiry = time.time()

        self.headers = {
            # Request headers
            'Content-Type': 'application/x-www-form-urlencoded'
        }

        self.body = {
            # Request body
            'grant_type': 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'client_id': self.client_id,
            'assertion': self.client_secret
        }

    def regen_token(self):
        # Sends request to regenerate token
        try:
            # Get key from API
            response = requests.post(f"{oauth_base}/token",
                                     headers=self.headers,
                                     data=self.body,
                                     ).json()
        except:
            raise Exception("Sending request failed, check connection.")

        # API errors are inconsistent, easiest way to catch them
        if "error" in response:
            raise Exception(
                "Token requesting failed, cannot proceed with any Zettle actions, exiting.\n"
                f"Error raised was {response}")

        # Get token from response and set expiry - should be valid for 7200s but being cautious
        self.token = response["access_token"]
        self.token_expiry = time.time() + 7100

    def __call__(self, r):

        # If token expiry is now or in past, call regenToken
        if self.token_expiry <= time.time():
            self.regen_token()
        # Set headers and return complete requests.Request object
        r.headers["Authorization"] = f"Bearer {self.token}"
        return r


class ZettleClient:
    def __init__(self, client_id: str, client_secret: str, config_file_path: str = "/home/fpp/media/config/plugin.fpp-zettle.json"):
        self.client_id = client_id
        self.client_secret = client_secret
        self.auth = TokenAuth(client_id=client_id, client_secret=client_secret)
        # While testing use a local file?
        self.config_file = Path("./plugin.fpp-zettle.json")
        # self.config_file = Path(config_file_path)
        # TODO why do i need a config file?? Let Fpp handle that & pass in details?
        if self.config_file_exists():
            self.read_config_file()
        else:
            self.settings = dict(client_id=client_id, client_secret=client_secret)
            self.create_config_file()

    def _try_request(self, method, url, payload: dict = None, is_json: bool=True):
        try:
            headers = {}
            if is_json:
                headers={'Content-Type': 'application/json'}
            response = request(method, url, data=json.dumps(payload), auth=self.auth, headers=headers)
        except RequestException as r:
            print(f"There was an issue connecting to consul at {url}: {str(r.args[0])}")
            raise r

        return response

    def create_config_file(self):
        with open(self.config_file, mode="w") as file:
            json.dump(self.settings, file)

    def config_file_exists(self):
        return True if exists(self.config_file) else False

    def read_config_file(self):
        with open(self.config_file, mode="r") as file:
            self.settings = json.load(file)
        return self.settings

    def update_config_file_setting(self, key, value):
        with open(self.config_file, mode="r") as file:
            self.settings = json.load(file)

        self.settings[key] = value

        with open(self.config_file, mode="w") as file:
            json.dump(self.settings, file, indent=2)

    def get_subscriptions(self):
        response = self._try_request("GET", subscriptions_url).json()
        self.update_config_file_setting("subscriptions", response)
        return json.dumps(response, indent=2)

    def create_purchase_subscription(self, destination, email):
        payload = {
            "uuid": str(uuid1()),
            "transportName": "WEBHOOK",
            "eventNames": ["PurchaseCreated"],
            "destination": destination,
            "contactEmail": email
        }

        response = self._try_request("POST", subscriptions_url, payload)
        self.get_subscriptions()
        return json.dumps(response.json(), indent=2)

    def get_org_id(self):
        user_url = f"{oauth_base}/users/self"
        try:
            response = self._try_request("GET", user_url)
            org_id = response.json()["organizationUuid"]
            self.update_config_file_setting("organizationUuid", org_id)
            return org_id
        except:
            print("An error occurred fetching user info.")
            return ""
