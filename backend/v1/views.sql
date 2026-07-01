CREATE OR REPLACE VIEW view_jobs AS 
SELECT
    jobs.ID AS ID,
    jobs.slug AS slug,
    jobs.title AS title,
    jobs.description AS description,
    jobs.cover_url AS cover_url,
    jobs.duration AS duration,    
    jobs.created_at AS created_at,
    skills.ID AS skill_id,
    skills.name AS skill_name
FROM jobs
JOIN job_skills ON jobs.ID = job_skills.job_id
JOIN skills ON skills.ID = job_skills.skill_id