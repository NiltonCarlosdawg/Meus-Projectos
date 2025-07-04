import { FiEye, FiUser } from "react-icons/fi"
import { PolarAngleAxis, PolarGrid, Radar, RadarChart, ResponsiveContainer, Tooltip } from "recharts"

const UsageRadar = () => {
  const Data = [
    {
      name: "Page A",
      desktop: 15788,
      mobile: 3988,
      max:2000
    },
    {
      name: "Page B",
      desktop: 3000,
      mobile: 7222,
      max:500
    },
    {
      name: "Page C",
      desktop: 1,
      mobile: 6398,
      max:200
    },
    {
      name: "Page E",
      desktop: 5788,
      mobile: 26988,
      max:8000
    },
    {
      name: "Page F",
      desktop: 10788,
      mobile: 31988,
      max:21000
    },
    {
      name: "Page G",
      desktop: 10788,
      mobile: 31988,
      max:21000
    },
  ]
  
  return (

    <div className="col-span-4 overflow-y-hidden rounded border border-stone-300">
      <div className="p-4">
        <h3 className="flex items-center gap-1.5 font-medium"><FiEye/> Usage</h3>
      </div>
      <div className="h-64 px-4">
        <ResponsiveContainer width="100%" height="100%">
          <RadarChart cx="50%" cy="50%" outerRadius="80%" data={Data} >
            <PolarGrid/>
            
            <PolarAngleAxis dataKey="name" className="text-xs font-bold"/>

            <Tooltip wrapperClassName="text-sm rounded" labelClassName="text-xs text-stone-500"/>
            <Radar dataKey="mobile" stroke="#18181b" fill="#18181b" fillOpacity={0.2}/>
            <Radar dataKey="desktop" stroke="#18181b" fill="#18181b" fillOpacity={0.2}/>
    
          </RadarChart>
        </ResponsiveContainer>
      </div>

    </div>

  )
}

export default UsageRadar