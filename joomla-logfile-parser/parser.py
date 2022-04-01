import sys
from datetime import *
import requests
from collections import Counter


path_to_logfile = "joomla-logfile-parser/not_user_log.log"
timeframe = int(sys.argv[1])
allowed_tries = int(sys.argv[2])

def check_logfile():
    f = open(path_to_logfile, "r")
    lines = f.read().split("\n")[6:]
    acts = list(map(lambda x: to_action(x), lines))
    login_attempts_within_x = list(filter(lambda action: action.is_login_attempt and is_within(datetime.utcnow(), action.Date, timeframe), acts))
    ip_counter = Counter(a.IP for a in login_attempts_within_x)
    print(ip_counter)

    for i in ip_counter:
        if ip_counter[i] >= allowed_tries:
            print(i)
            requests.post('http://localhost:8080/api/block/{}?key=4538f19b-9575-4cca-beba-a4382a37707d'.format(i))

def to_action(str):
    str_bits = str.split("\t")
    print(str_bits)
    return Action(str_bits[0], str_bits[1][5:], str_bits[2])

def is_within(dt1, dt2, tf):
    delta = dt1 - dt2
    return delta.total_seconds() / 60 < tf

class Action:
    def __init__(self, Date, IP, Act):
        # self.Id = Id
        self.Act = Act
        # self.Extension = Extension
        self.Date = datetime.strptime(Date, "%Y-%m-%dT%H:%M:%S+00:00")
        # self.Date = Date
        # self.Name = Name
        self.IP = IP
    
    def is_login_attempt(self):
        return self.Act == "Username and password do not match or you do not have an account yet.";

check_logfile()