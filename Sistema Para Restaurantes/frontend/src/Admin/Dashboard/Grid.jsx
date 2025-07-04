import ActivityGraph from "./ActivityGraph"
import RecentTrade from "./RecentTrade"
import StateCards from "./StateCards"
import UsageRadar from "./UsageRadar"

const Grid = () => {
  return (
    <div className="px-4 grid gap-3 grid-cols-12">
      <StateCards/>
      <ActivityGraph/>
      <UsageRadar/>
      <RecentTrade/>
    </div>
  )
}

export default Grid