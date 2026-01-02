import requests
import datetime
import time
import json

URL = "http://localhost/graphql"

def run_mutation(query, variables=None):
    try:
        response = requests.post(URL, json={'query': query, 'variables': variables})
        response.raise_for_status()
        data = response.json()
        if 'errors' in data:
            # Check for "already exists" errors (clubs or members)
            is_exists = False
            for err in data['errors']:
                msg = err.get('message', '')
                if "already exists" in msg or "Duplicate key" in msg:
                    is_exists = True
                    break
            
            if is_exists:
                print(f"Entity already exists. Continuing... Error: {data['errors'][0].get('message')}")
                return data

            print(f"Errors for variables {variables}: {data['errors']}")
            with open("error_log.txt", "a") as f:
                f.write(f"Query: {query}\nVariables: {variables}\n")
                f.write(json.dumps(data, indent=2))
                f.write("\n" + "="*80 + "\n")
            raise Exception("GraphQL Error")
        return data
    except Exception as e:
        print(f"Exception: {e}")
        return None

# 1. Create Clubs
clubs = [
    {"cid": "club1", "name": "Coding Club", "code": "CC1", "category": "technical", "email": "club1@iiit.ac.in"},
    {"cid": "club2", "name": "Dance Club", "code": "DC1", "category": "cultural", "email": "club2@iiit.ac.in"},
    {"cid": "club3", "name": "Music Club", "code": "MC1", "category": "cultural", "email": "club3@iiit.ac.in"},
]

print("--- Creating Clubs ---")
for c in clubs:
    mutation = """
    mutation CreateClub($input: FullClubInput!) {
        createClub(clubInput: $input) {
            cid
            name
        }
    }
    """
    variables = {
        "input": {
            "cid": c["cid"],
            "code": c["code"],
            "name": c["name"],
            "email": c["email"],
            "category": c["category"],
            "tagline": f"The best {c['name']}",
            "description": f"This is the description for {c['name']}",
            "socials": {}
        }
    }
    print(f"Creating {c['name']}...")
    res = run_mutation(mutation, variables)
    print(res)

# 2. Add Members - 3 per club
print("\n--- Creating Members ---")
created_pocs = {}
for c in clubs:
    # Adding POC member first (important for events)
    members = ["poc", "mem1", "mem2"]
    created_pocs[c["cid"]] = f"{c['cid']}_poc"
    for i, m in enumerate(members):
        uid = f"{c['cid']}_{m}"
        mutation = """
        mutation CreateMember($input: FullMemberInput!) {
            createMember(memberInput: $input) {
                uid
                cid
                roles {
                    name
                }
            }
        }
        """
        variables = {
            "input": {
                "cid": c["cid"],
                "uid": uid,
                "roles": [
                    {"name": "Core Member", "startYear": 2024, "endYear": None}
                ]
            }
        }
        print(f"Adding member {uid} to {c['cid']}...")
        res = run_mutation(mutation, variables)
        print(res)
    
    # DEBUG: Check if member exists
    poc_uid_debug = created_pocs[c["cid"]]
    print(f"DEBUG: Checking if POC {poc_uid_debug} exists via query...")
    chk_mutation = """
    query Member($memberInput: SimpleMemberInput!) {
        member(memberInput: $memberInput) {
            uid
            cid
        }
    }
    """
    chk_vars = {
        "memberInput": {"cid": c["cid"], "uid": poc_uid_debug, "rid": None}
    }
    res = run_mutation(chk_mutation, chk_vars)
    print(f"DEBUG Member Check Result: {res}")


# 3. Add Events - 2 per club
print("\n--- Creating Events ---")
for c in clubs:
    poc_uid = created_pocs[c["cid"]]
    for i in range(1, 3):
        mutation = """
        mutation CreateEvent($details: InputEventDetails!) {
            createEvent(details: $details) {
                _id
                name
                code
            }
        }
        """
        # ISO format for strawberry datetime scalar usually works
        start_time = (datetime.datetime.now() + datetime.timedelta(days=i)).isoformat()
        end_time = (datetime.datetime.now() + datetime.timedelta(days=i, hours=2)).isoformat()
        
        variables = {
            "details": {
                "name": f"Event {i} by {c['name']}",
                "clubid": c["cid"],
                "datetimeperiod": [start_time, end_time],
                "poc": poc_uid,
                "description": f"Description for Event {i}",
                "mode": "offline",
                "location": ["h101"], 
                "audience": ["ug1"],
                "population": 100
            }
        }
        print(f"Creating event {i} for {c['cid']}...")
        res = run_mutation(mutation, variables)
        print(res)

        if res and 'data' in res and 'createEvent' in res['data']:
            eid = res['data']['createEvent']['_id']
            print(f"Approving event {eid}...")
            # Approve Mutation
            approve_mutation = """
            mutation ApproveEvent($eventid: String!, $cc_approver: String!) {
                progressEvent(eventid: $eventid, ccProgressBudget: true, ccProgressRoom: true, ccApprover: $cc_approver) {
                    _id
                    status {
                        state
                    }
                }
            }
            """
            approve_vars = {
                "eventid": eid,
                "cc_approver": "cc"  # From auth bypass
            }
            res_approve = run_mutation(approve_mutation, approve_vars)
            print(f"Approved: {res_approve}")
