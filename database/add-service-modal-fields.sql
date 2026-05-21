ALTER TABLE services
  ADD COLUMN summary TEXT NULL AFTER description,
  ADD COLUMN content TEXT NULL AFTER summary,
  ADD COLUMN benefits TEXT NULL AFTER content,
  ADD COLUMN financial_benefits TEXT NULL AFTER benefits,
  ADD COLUMN roi_note TEXT NULL AFTER financial_benefits,
  ADD COLUMN video_url VARCHAR(255) NULL AFTER roi_note;
