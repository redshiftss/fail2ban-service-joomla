import sys
from datetime import *
import requests
from collections import Counter


path_to_logfile = "joomla-logfile-parser/logfile.csv"
timeframe = int(sys.argv[1])
allowed_tries = int(sys.argv[2])

def check_logfile():
    f = open(path_to_logfile, "r")
    lines = f.read().split("\n")[1:]
    acts = list(map(lambda x: to_action(x), lines))
    login_attempts_within_x = list(filter(lambda action: action.is_login_attempt and is_within(datetime.utcnow(), action.Date, timeframe), acts))
    ip_counter = Counter(a.IP for a in login_attempts_within_x)
    print(ip_counter)

    for i in ip_counter:
        if ip_counter[i] >= allowed_tries:
            print(i)
            r = requests.post('127.0.0.1/api/block/{}'.format(i))

def to_action(str):
    str_bits = str.split(",")
    return Action(str_bits[0], str_bits[1], str_bits[2], str_bits[3][1:-1], str_bits[4], str_bits[5])

def is_within(dt1, dt2, tf):
    delta = dt1 - dt2
    return delta.total_seconds() / 60 < tf

class Action:
    def __init__(self, Id, Act, Extension, Date, Name, IP):
        self.Id = Id
        self.Act = Act
        self.Extension = Extension
        self.Date = datetime.strptime(Date, "%Y-%m-%d %H:%M:%S UTC")
        # self.Date = Date
        self.Name = Name
        self.IP = IP
    
    def is_login_attempt(self):
        return self.Act == "A failed attempt was made to login as admin to site";

check_logfile()