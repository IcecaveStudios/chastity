local key   = KEYS[1]
local token = ARGV[1]
local ttl   = ARGV[2]

if token ~= redis.call('GET', key) then
    return 0
end

ttl = ttl + redis.call('PTTL', key)
redis.call('PEXPIRE', key, ttl)

return ttl
