import csv

def convert_users():
    # Read the tab-separated file
    users = []
    with open('test_users.csv', 'r') as f:
        reader = csv.reader(f, delimiter='\t')
        next(reader)  # Skip header
        for row in reader:
            if len(row) >= 4 and row[2] != '0':  # Skip rows with empty passwords
                # Start IDs from 1001 to avoid conflicts
                users.append([
                    str(1000 + len(users) + 1),  # New ID
                    row[1],                      # Username
                    row[2],                      # Password
                    row[3]                       # UserType
                ])

    # Write the comma-separated file
    with open('formatted_users.csv', 'w', newline='') as f:
        writer = csv.writer(f)
        writer.writerow(['id', 'username', 'password', 'userType'])
        writer.writerows(users)

    print(f"Converted {len(users)} users to formatted_users.csv")

if __name__ == '__main__':
    convert_users() 