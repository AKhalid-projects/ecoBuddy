-- Create the ecoFacilityStatus table if it doesn't exist
CREATE TABLE IF NOT EXISTS ecoFacilityStatus (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    facilityId INTEGER NOT NULL,
    statusComment TEXT NOT NULL,
    userId INTEGER NOT NULL,
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (facilityId) REFERENCES ecoFacilities(id) ON DELETE CASCADE,
    FOREIGN KEY (userId) REFERENCES ecoUser(id) ON DELETE CASCADE
);
-- Create index for faster queries
CREATE INDEX IF NOT EXISTS idx_facility_status_facilityId ON ecoFacilityStatus(facilityId);
CREATE INDEX IF NOT EXISTS idx_facility_status_timestamp ON ecoFacilityStatus(timestamp);