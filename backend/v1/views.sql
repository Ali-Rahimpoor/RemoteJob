CREATE OR REPLACE VIEW view_jobs AS 
SELECT
    jobs.ID AS ID,
    jobs.slug AS slug,
    jobs.title AS title,
    jobs.description AS description,
    jobs.excerpt AS excerpt,
    jobs.status AS status,
    jobs.min_score as min_score,
    jobs.cover_url AS cover_url,
    jobs.duration AS duration,    
    jobs.salary AS salary,
    jobs.created_at AS created_at,
    skills.ID AS skill_id,
    skills.name AS skill_name,
    users.ID AS user_id,
    users.full_name AS user_fullname,
    users.avatar AS user_avatar,
    users.score AS user_score
FROM jobs
JOIN job_skills ON jobs.ID = job_skills.job_id
JOIN skills ON skills.ID = job_skills.skill_id
JOIN users ON jobs.user_id = users.ID